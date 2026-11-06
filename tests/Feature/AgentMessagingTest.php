<?php

namespace Tests\Feature;

use App\Livewire\Agent\CommissionLedger;
use App\Models\DealLedger;
use App\Models\GatewayDevice;
use App\Models\MessageLog;
use App\Models\MessagingSetting;
use App\Models\User;
use App\Services\Messaging\Messenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

/** Admin jo tareeqa chune (WhatsApp link / API / SMS), wahi agents par bhi chale */
class AgentMessagingTest extends TestCase
{
    use RefreshDatabase;

    private User $agent;

    protected function setUp(): void
    {
        parent::setUp();
        Messenger::flush();
        $this->agent = User::factory()->create(['role' => 'agent', 'is_approved' => true, 'wallet_balance' => 10, 'name' => 'Bilal']);
    }

    private function useDriver(string $driver, array $config = []): void
    {
        MessagingSetting::current()->update(['driver' => $driver, 'config' => $config, 'owner_daily_limit' => 0]);
        Messenger::flush();
    }

    private function deal(): DealLedger
    {
        return DealLedger::create([
            'agent_id' => $this->agent->id, 'deal_title' => '5 Marla', 'client_name' => 'Hamza', 'client_phone' => '0311-1234567',
            'deal_date' => '2026-11-01', 'deal_value' => 100, 'total_commission' => 10, 'pending_amount' => 5000,
        ]);
    }

    public function test_link_mode_opens_whatsapp_like_before(): void
    {
        $deal = $this->deal();

        Livewire::actingAs($this->agent)->test(CommissionLedger::class)
            ->call('sendPaymentReminder', $deal->id)
            ->assertDispatched('trigger-whatsapp', fn ($name, $p) => str_starts_with($p['appUrl'], 'whatsapp://send?phone=923111234567&text='));

        $this->assertSame(0, MessageLog::count());
    }

    public function test_sms_gateway_mode_sends_sms_from_agent_wallet(): void
    {
        $this->useDriver('gateway', ['sms_rate' => 1]);
        GatewayDevice::register('Office');
        $deal = $this->deal();

        Livewire::actingAs($this->agent)->test(CommissionLedger::class)
            ->call('sendPaymentReminder', $deal->id)
            ->assertNotDispatched('trigger-whatsapp');

        $log = MessageLog::sole();
        $this->assertSame([$this->agent->id, 'queued', '923111234567', 'agent_commission'], [$log->owner_id, $log->status, $log->to, $log->context['source']]);
        $this->assertEquals(10 - (float) $log->cost, (float) $this->agent->fresh()->wallet_balance);
        $this->assertSame('SMS', Messenger::channelName());
    }

    public function test_whatsapp_api_mode_sends_directly_and_never_falls_back_to_link(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::sequence()->push(['messages' => [['id' => 'wamid.9']]])->push(['error' => ['message' => 'outside 24h window']], 400)]);
        $this->useDriver('whatsapp_cloud', ['phone_number_id' => '1', 'token' => 'T']);
        $deal = $this->deal();

        $c = Livewire::actingAs($this->agent)->test(CommissionLedger::class);
        $c->call('sendPaymentReminder', $deal->id)->assertNotDispatched('trigger-whatsapp');
        $c->call('sendPaymentReminder', $deal->id)->assertNotDispatched('trigger-whatsapp');

        $this->assertSame(['sent', 'failed'], MessageLog::orderBy('id')->pluck('status')->all());
        Http::assertSent(fn ($r) => $r['type'] === 'text' && str_contains($r['text']['body'], 'Hamza'));
    }

    public function test_mobile_api_follows_admin_setting(): void
    {
        config(['app.url' => 'http://localhost']);
        Sanctum::actingAs($this->agent);

        $this->getJson('http://localhost/api/v1/messages/channel')->assertOk()->assertJsonPath('data.mode', 'link');
        $this->postJson('http://localhost/api/v1/messages', ['phone' => '03111234567', 'message' => 'Salam'])
            ->assertOk()->assertJsonPath('data.status', 'link')->assertJsonPath('data.link', 'https://wa.me/923111234567?text=Salam');

        $this->useDriver('gateway', ['sms_rate' => 20]);
        GatewayDevice::register('Office');
        $this->getJson('http://localhost/api/v1/messages/channel')->assertJsonPath('data.channel', 'SMS')->assertJsonPath('data.sms_rate', 20);
        // Rs. 10 balance, Rs. 20 ka SMS
        $this->postJson('http://localhost/api/v1/messages', ['phone' => '03111234567', 'message' => 'Salam'])
            ->assertStatus(402)->assertJsonPath('data.status', 'no_balance');

        $this->agent->update(['wallet_balance' => 100]);
        $this->postJson('http://localhost/api/v1/messages', ['phone' => '03111234567', 'message' => 'Salam'])
            ->assertOk()->assertJsonPath('data.status', 'queued');
        $this->assertEquals(80, (float) $this->agent->fresh()->wallet_balance);

        // Tayyar paighamat ke WhatsApp links SMS mode mein band; text milta hai
        $deal = $this->deal();
        $this->getJson('http://localhost/api/v1/agent/deals')->assertOk()
            ->assertJsonPath('data.0.reminder_whatsapp_url', null)
            ->assertJsonPath('data.0.reminder_message', fn ($m) => str_contains($m, 'Hamza'));
    }

    public function test_agent_dashboard_lead_button_uses_sms_even_when_phone_offline(): void
    {
        $this->useDriver('gateway', ['sms_rate' => 1]); // koi phone nahi joda
        $lead = \App\Models\Lead::create([
            'agent_id' => $this->agent->id, 'client_name' => 'Hamza', 'client_phone' => '03111234567',
            'status' => 'new', 'priority' => 'hot', 'source' => 'portal',
        ]);

        Livewire::actingAs($this->agent)->test(\App\Livewire\Agent\AgentDashboard::class)
            ->assertSeeHtml("sendLeadMessage({$lead->id}, 'portal')")
            ->assertDontSeeHtml('wa.me/923111234567')
            ->call('sendLeadMessage', $lead->id, 'portal')
            ->assertNotDispatched('trigger-whatsapp');

        $this->assertSame(['queued', 'agent_dashboard'], [MessageLog::sole()->status, MessageLog::sole()->context['source']]);
        $this->assertSame('contacted', $lead->fresh()->status);
    }
}
