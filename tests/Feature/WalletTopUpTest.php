<?php

namespace Tests\Feature;

use App\Livewire\Admin\TopUpRequests;
use App\Livewire\Wallet;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

/** Agent / town owner top-up ki darkhwast deta hai, admin manzoor karta hai, tab balance barhta hai */
class WalletTopUpTest extends TestCase
{
    use RefreshDatabase;

    private array $files = [];

    protected function tearDown(): void
    {
        // Test ke screenshots public/ss mein na rahein
        foreach (WalletTransaction::whereNotNull('screenshot')->pluck('screenshot') as $path) {
            @unlink(public_path($path));
        }
        parent::tearDown();
    }

    private function method(): PaymentMethod
    {
        return PaymentMethod::create(['method_name' => 'EasyPaisa', 'account_title' => 'Assan Zameen', 'account_number' => '03000000000', 'is_active' => true]);
    }

    private function request(User $user, PaymentMethod $m, int $amount = 2000)
    {
        return Livewire::actingAs($user)->test(Wallet::class)
            ->call('openForm')
            ->set('amount', $amount)
            ->set('methodId', $m->id)
            ->set('senderTitle', 'Ali')
            ->set('senderNumber', '03001112222')
            ->set('screenshot', UploadedFile::fake()->image('pay.png'))
            ->call('requestTopUp');
    }

    public function test_agent_and_town_owner_see_wallet_page(): void
    {
        $agent = User::factory()->create(['role' => 'agent', 'is_approved' => true, 'wallet_balance' => 55]);
        $owner = User::factory()->create(['role' => 'town_owner', 'is_approved' => true]);

        $this->actingAs($agent)->get(route('agent.wallet'))->assertOk()->assertSee('Rs. 55.00');
        $this->actingAs($owner)->get(route('town_owner.wallet'))->assertOk()->assertSee(__('My Wallet'));
    }

    public function test_staff_cannot_open_owner_wallet(): void
    {
        $owner = User::factory()->create(['role' => 'town_owner', 'is_approved' => true]);
        $staff = User::factory()->create(['role' => 'town_owner', 'is_approved' => true, 'owner_id' => $owner->id, 'permissions' => ['recovery']]);

        $this->actingAs($staff)->get(route('town_owner.wallet'))->assertForbidden();
    }

    public function test_request_waits_for_admin_then_balance_increases(): void
    {
        $m = $this->method();
        $agent = User::factory()->create(['role' => 'agent', 'is_approved' => true, 'wallet_balance' => 0]);

        $this->request($agent, $m, 2000)->assertHasNoErrors();

        $tx = WalletTransaction::sole();
        $this->assertSame(['deposit', 'pending', $agent->id, 'EasyPaisa'], [$tx->type, $tx->status, $tx->user_id, $tx->payment_method]);
        $this->assertFileExists(public_path($tx->screenshot));
        $this->assertEquals(0, (float) $agent->fresh()->wallet_balance); // abhi nahi

        $admin = User::factory()->create(['role' => 'admin']);
        Livewire::actingAs($admin)->test(TopUpRequests::class)->assertSee($agent->name)->call('approve', $tx->id);

        $this->assertSame('approved', $tx->fresh()->status);
        $this->assertEquals(2000, (float) $agent->fresh()->wallet_balance);

        // Dobara approve se dobara paise nahi
        Livewire::actingAs($admin)->test(TopUpRequests::class)->call('approve', $tx->id);
        $this->assertEquals(2000, (float) $agent->fresh()->wallet_balance);
    }

    public function test_rejected_request_does_not_add_money_and_shows_reason(): void
    {
        $m = $this->method();
        $owner = User::factory()->create(['role' => 'town_owner', 'is_approved' => true, 'wallet_balance' => 0]);
        $this->request($owner, $m, 500);
        $tx = WalletTransaction::sole();

        $admin = User::factory()->create(['role' => 'admin']);
        Livewire::actingAs($admin)->test(TopUpRequests::class)
            ->call('openRejectModal', $tx->id)->set('rejectionReason', 'Screenshot saaf nahi')->call('reject');

        $this->assertSame('rejected', $tx->fresh()->status);
        $this->assertEquals(0, (float) $owner->fresh()->wallet_balance);
        Livewire::actingAs($owner)->test(Wallet::class)->assertSee('Screenshot saaf nahi');
    }

    public function test_validation_and_pending_limit(): void
    {
        $m = $this->method();
        $agent = User::factory()->create(['role' => 'agent', 'is_approved' => true]);

        Livewire::actingAs($agent)->test(Wallet::class)->set('amount', 50)->call('requestTopUp')
            ->assertHasErrors(['amount', 'methodId', 'senderTitle', 'senderNumber', 'screenshot']);

        foreach (range(1, Wallet::MAX_PENDING) as $i) {
            $this->request($agent, $m)->assertHasNoErrors();
        }
        $this->request($agent, $m)->assertHasErrors('amount');
        $this->assertSame(Wallet::MAX_PENDING, WalletTransaction::count());
    }
}
