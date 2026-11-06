<?php

namespace Tests\Feature;

use App\Models\GatewayDevice;
use App\Models\MessageLog;
use App\Models\MessagingSetting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Messaging\Messenger;
use App\Services\Messaging\SmsGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsGatewayTest extends TestCase
{
    use RefreshDatabase;

    private array $vars = ['name' => 'Hamza', 'place' => 'Furqan Garden — Plot #7', 'amount' => '52,222', 'due_date' => '10 Nov 2026', 'portal' => ''];

    private string $token;

    private GatewayDevice $device;

    protected function setUp(): void
    {
        parent::setUp();
        MessagingSetting::current()->update(['driver' => 'gateway', 'config' => ['sms_rate' => 2, 'sms_delay_ms' => 1500], 'owner_daily_limit' => 0]);
        Messenger::flush();
        [$this->device, $this->token] = GatewayDevice::register('Office phone', '03001112222', 300);
    }

    private function owner(float $balance): User
    {
        return User::factory()->create(['role' => 'town_owner', 'wallet_balance' => $balance, 'is_approved' => true]);
    }

    private function pull(array $body = [], ?string $token = null)
    {
        return $this->withToken($token ?? $this->token)->postJson('http://localhost/api/gateway/pull', $body);
    }

    public function test_segments_count_english_and_urdu(): void
    {
        $this->assertSame(1, SmsGateway::segments(str_repeat('a', 160)));
        $this->assertSame(2, SmsGateway::segments(str_repeat('a', 161)));
        $this->assertSame(1, SmsGateway::segments(str_repeat('ب', 70)));
        $this->assertSame(2, SmsGateway::segments(str_repeat('ب', 71)));
        $this->assertSame(2, SmsGateway::segments('Plot — ' . str_repeat('a', 70))); // em-dash GSM nahi: Unicode
    }

    public function test_user_is_charged_and_phone_sends_it(): void
    {
        $owner = $this->owner(10);

        $r = Messenger::send('0311-1234567', 'installment_due_soon', $this->vars, ['installment_id' => 4], $owner->id);

        $this->assertTrue($r['ok']);
        $this->assertSame('queued', $r['status']);
        $log = MessageLog::sole();
        $this->assertSame('gateway', $log->driver);
        $cost = $log->segments * 2;
        $this->assertEquals($cost, (float) $log->cost);
        $this->assertEquals(10 - $cost, (float) $owner->fresh()->wallet_balance);
        $this->assertSame(['message_fee', (string) $log->id], [WalletTransaction::sole()->type, WalletTransaction::sole()->reference_id]);

        // Phone utha leta hai
        $res = $this->pull(['battery' => 80])->assertOk()->assertJsonPath('delay_ms', 1500)->json('messages');
        $this->assertSame([['id' => $log->id, 'to' => '+923111234567', 'body' => $log->body]], $res);
        $this->assertSame('sending', $log->fresh()->status);
        $this->assertSame(80, $this->device->fresh()->last_status['battery']);
        // Doosri dafa wahi paigham nahi milta
        $this->assertSame([], $this->pull()->json('messages'));

        $this->withToken($this->token)->postJson('http://localhost/api/gateway/report', ['results' => [['id' => $log->id, 'status' => 'sent']]])
            ->assertOk()->assertJsonPath('accepted', 1);
        $this->assertSame('sent', $log->fresh()->status);
        $this->assertNotNull($log->fresh()->sent_at);
    }

    public function test_not_enough_balance_is_not_queued_and_not_charged(): void
    {
        $owner = $this->owner(1);

        $r = Messenger::send('03111234567', 'installment_due_soon', $this->vars, [], $owner->id);

        $this->assertFalse($r['ok']);
        $this->assertSame('no_balance', $r['status']);
        $this->assertStringContainsString('Wallet', $r['error']);
        $this->assertNotNull($r['link']);
        $this->assertEquals(1, (float) $owner->fresh()->wallet_balance);
        $this->assertSame(0, WalletTransaction::count());
        $this->assertSame([], $this->pull()->json('messages'));
    }

    public function test_failed_sms_is_retried_then_refunded(): void
    {
        $owner = $this->owner(50);
        Messenger::send('03111234567', 'installment_due_soon', $this->vars, [], $owner->id);
        $id = MessageLog::sole()->id;
        $charged = 50 - (float) $owner->fresh()->wallet_balance;
        $this->assertGreaterThan(0, $charged);

        for ($i = 1; $i <= SmsGateway::MAX_ATTEMPTS; $i++) {
            $this->assertSame($id, $this->pull()->json('messages.0.id'));
            $this->withToken($this->token)->postJson('http://localhost/api/gateway/report', ['results' => [['id' => $id, 'status' => 'failed', 'error' => 'Signal nahi']]])->assertOk();
            $this->assertSame($i < SmsGateway::MAX_ATTEMPTS ? 'queued' : 'failed', MessageLog::find($id)->status);
        }

        $this->assertEquals(50, (float) $owner->fresh()->wallet_balance);
        $this->assertTrue(MessageLog::find($id)->refunded);
        $this->assertSame(['message_fee', 'refund'], WalletTransaction::orderBy('id')->pluck('type')->all());

        // Dobara report karne par dobara paise wapas nahi
        $this->withToken($this->token)->postJson('http://localhost/api/gateway/report', ['results' => [['id' => $id, 'status' => 'failed']]])->assertJsonPath('accepted', 0);
        $this->assertEquals(50, (float) $owner->fresh()->wallet_balance);
    }

    public function test_phone_that_goes_silent_releases_its_messages(): void
    {
        $owner = $this->owner(50);
        Messenger::send('03111234567', 'installment_due_soon', $this->vars, [], $owner->id);
        $this->pull();

        [$other, $otherToken] = GatewayDevice::register('Doosra phone');
        $this->travel(SmsGateway::CLAIM_TIMEOUT_MINUTES + 1)->minutes();

        $this->assertCount(1, $this->pull([], $otherToken)->json('messages'));
        $this->assertSame($other->id, MessageLog::sole()->device_id);
        // Pehla phone baad mein jaage to us ki report nahi mani jati
        $this->withToken($this->token)->postJson('http://localhost/api/gateway/report', ['results' => [['id' => MessageLog::sole()->id, 'status' => 'sent']]])->assertJsonPath('accepted', 0);
    }

    public function test_device_daily_limit_inactive_phone_and_bad_token(): void
    {
        $owner = $this->owner(100);
        $this->device->update(['daily_limit' => 2]);
        foreach (range(1, 3) as $n) {
            Messenger::send('0311123456' . $n, 'portal_link', ['name' => 'A', 'place' => 'B', 'url' => 'https://x.y'], [], $owner->id);
        }

        $this->assertCount(2, $this->pull()->json('messages'));
        $this->assertCount(0, $this->pull()->json('messages'));

        $this->device->update(['daily_limit' => 0, 'is_active' => false]);
        $this->pull()->assertJsonPath('active', false)->assertJsonPath('messages', []);

        $this->withoutToken()->postJson('http://localhost/api/gateway/pull')->assertStatus(401);
        $this->pull([], 'gw_wrong')->assertStatus(401);
    }

    public function test_admin_messages_are_free_and_no_phone_means_not_automatic(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'wallet_balance' => 0]);
        $this->assertTrue(Messenger::send('03111234567', 'installment_due_soon', $this->vars, [], $admin->id)['ok']);
        $this->assertEquals(0, (float) MessageLog::sole()->cost);

        $this->device->update(['is_active' => false]);
        Messenger::flush();
        $this->assertSame('link', Messenger::send('03111234567', 'installment_due_soon', $this->vars, [], $admin->id)['status']);
    }

    public function test_admin_cancel_refunds_user(): void
    {
        $owner = $this->owner(20);
        Messenger::send('03111234567', 'installment_due_soon', $this->vars, [], $owner->id);

        $this->assertTrue(SmsGateway::cancel(MessageLog::sole()));
        $this->assertSame('cancelled', MessageLog::sole()->status);
        $this->assertEquals(20, (float) $owner->fresh()->wallet_balance);
        $this->assertSame([], $this->pull()->json('messages'));
    }
}
