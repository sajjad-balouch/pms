<?php

namespace Tests\Feature;

use App\Models\MessageLog;
use App\Models\MessageTemplate;
use App\Models\MessagingSetting;
use App\Services\Messaging\Messenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MessengerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Messenger::flush();
    }

    private function useDriver(string $driver, array $config, int $limit = 200): void
    {
        MessagingSetting::current()->update(['driver' => $driver, 'config' => $config, 'owner_daily_limit' => $limit]);
        Messenger::flush();
    }

    private array $vars = ['name' => 'Hamza', 'place' => 'Furqan Garden — Plot #7', 'amount' => '52,222', 'due_date' => '10 Nov 2026', 'portal' => ''];

    public function test_default_is_link_only_and_admin_can_change_the_wording(): void
    {
        $r = Messenger::send('0311-1234567', 'installment_due_soon', $this->vars);
        $this->assertSame('link', $r['status']);
        $this->assertStringStartsWith('https://wa.me/923111234567?text=', $r['link']);
        $this->assertSame('Assalam o Alaikum Hamza, Furqan Garden — Plot #7: yaad-dehani — aap ki agli qist Rs. 52,222 10 Nov 2026 ko due hai. Shukriya.', $r['body']);
        $this->assertSame(0, MessageLog::count());

        MessageTemplate::create(['key' => 'installment_due_soon', 'body' => 'Salam {name}! Qist {amount} — {due_date}. {unknown}']);
        Messenger::flush();
        $this->assertSame('Salam Hamza! Qist 52,222 — 10 Nov 2026.', Messenger::render('installment_due_soon', $this->vars));
    }

    public function test_http_gateway_is_configured_only_from_settings(): void
    {
        Http::fake(['api.ultramsg.com/*' => Http::response(['sent' => 'true', 'id' => 77])]);
        $this->useDriver('http', [
            'url' => 'https://api.ultramsg.com/instance1/messages/chat', 'method' => 'POST', 'body_type' => 'form',
            'payload' => '{"token":"{token}","to":"{to}","body":"{message}"}', 'token' => 'SECRET', 'number_format' => '+92',
            'success_contains' => 'true',
        ]);

        $r = Messenger::send('03111234567', 'installment_due_soon', $this->vars, ['installment_id' => 5], ownerId: 9);

        $this->assertSame('sent', $r['status']);
        Http::assertSent(fn (Request $req) => $req['to'] === '+923111234567' && $req['token'] === 'SECRET' && str_contains($req['body'], 'Hamza'));
        $log = MessageLog::sole();
        $this->assertSame([9, 'sent', '77', 5], [$log->owner_id, $log->status, $log->provider_id, $log->context['installment_id']]);
        // Token database mein khula nahi
        $this->assertStringNotContainsString('SECRET', \DB::table('messaging_settings')->value('config'));
    }

    public function test_sms_gateway_with_get_and_local_number_format(): void
    {
        Http::fake(['sms.example.pk/*' => Http::response('OK: queued')]);
        $this->useDriver('http', ['url' => 'https://sms.example.pk/api/send?key={token}&to={to}&msg={message}', 'method' => 'GET', 'token' => 'K1', 'number_format' => '0', 'success_contains' => 'OK']);

        $this->assertSame('sent', Messenger::send('+92 311 1234567', 'portal_link', ['name' => 'Ali', 'place' => 'X', 'url' => 'https://a.b/c'])['status']);
        Http::assertSent(fn (Request $req) => str_contains($req->url(), 'to=03111234567') && str_contains($req->url(), 'msg=Assalam%20o%20Alaikum%20Ali'));
    }

    public function test_whatsapp_cloud_uses_approved_template_with_params_in_order(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.1']]])]);
        $this->useDriver('whatsapp_cloud', ['phone_number_id' => '123', 'token' => 'T', 'api_version' => 'v21.0']);
        MessageTemplate::create(['key' => 'installment_due_soon', 'body' => Messenger::template('installment_due_soon'), 'meta_template' => 'qist_reminder', 'meta_language' => 'ur']);
        Messenger::flush();

        $this->assertTrue(Messenger::send('03111234567', 'installment_due_soon', $this->vars)['ok']);
        Http::assertSent(function (Request $req) {
            $p = $req['template']['components'][0]['parameters'];

            return str_ends_with($req->url(), '/v21.0/123/messages') && $req['template']['name'] === 'qist_reminder'
                && $req['template']['language']['code'] === 'ur' && $p[0]['text'] === 'Hamza' && $p[4]['text'] === '-' && count($p) === 5;
        });
    }

    public function test_failure_and_daily_limit_are_logged_and_fall_back_to_link(): void
    {
        Http::fake(['*' => Http::sequence()->push('bad token', 401)->push('ok')]);
        $this->useDriver('http', ['url' => 'https://gw.test/send', 'payload' => '{"to":"{to}"}'], limit: 1);

        $r = Messenger::send('03111234567', 'installment_due_soon', $this->vars, ownerId: 3);
        $this->assertSame('failed', $r['status']);
        $this->assertStringContainsString('401', $r['error']);
        $this->assertNotNull($r['link']); // owner phir bhi link se bhej sakta hai

        $this->assertSame('sent', Messenger::send('03111234567', 'installment_due_soon', $this->vars, ownerId: 3)['status']);
        $this->assertSame('limited', Messenger::send('03111234567', 'installment_due_soon', $this->vars, ownerId: 3)['status']);
        $this->assertSame(['failed', 'sent', 'limited'], MessageLog::orderBy('id')->pluck('status')->all());
    }
}
