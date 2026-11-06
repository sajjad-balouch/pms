<?php

namespace Tests\Feature;

use App\Livewire\TownOwner\TownOwnerDashboard;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\Town;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TownOwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::forceCreate(['name' => 'Owner', 'email' => 'o@t.test', 'password' => bcrypt('x'), 'role' => 'town_owner', 'is_approved' => true, 'phone' => '03001234567']);
    }

    private function seedTown(User $owner): Plot
    {
        $city = \App\Models\City::forceCreate(['name' => 'Khanpur', 'slug' => 'khanpur']);
        $town = Town::forceCreate(['user_id' => $owner->id, 'city_id' => $city->id, 'name' => 'Furqan Garden', 'location' => 'Main Road', 'city' => 'Khanpur']);
        $plot = Plot::forceCreate(['town_id' => $town->id, 'plot_number' => '7', 'size' => '5 Marla', 'total_price' => 1500000, 'status' => 'booked']);
        // 45 din aur 100 din purani do late qistein, aur ek agle hafte
        foreach ([[-45, 10000], [-100, 20000], [3, 5000]] as $n => [$days, $amount]) {
            Installment::forceCreate(['plot_id' => $plot->id, 'installment_number' => $n + 1, 'amount' => $amount,
                'due_date' => now()->addDays($days)->toDateString(), 'status' => 'pending', 'buyer_phone' => '03111234567']);
        }

        return $plot;
    }

    public function test_owner_sees_simple_mobile_sections_and_aging(): void
    {
        $owner = $this->owner();
        $this->seedTown($owner);
        $this->actingAs($owner);

        $c = Livewire::test(TownOwnerDashboard::class)
            ->assertSee('Late aqsaat')
            ->assertSee('Agle 7 din')
            ->assertSee('Furqan Garden')
            ->assertSee('tel:03111234567', false)
            ->assertSee('Sab ko baari baari reminder', false);

        $aging = collect($c->viewData('aging'))->keyBy('label');
        $this->assertSame(1, $aging['31–60']['count']);
        $this->assertSame(1, $aging['90+']['count']);
        $this->assertEquals(30000, $c->viewData('overdueAmount'));
        foreach (['Qist wusooli', 'Kharcha', 'Team'] as $action) {
            $c->assertSee($action);
        }
    }

    public function test_when_admin_sets_an_api_dashboard_sends_reminders_directly(): void
    {
        $owner = $this->owner();
        $this->seedTown($owner);
        $this->actingAs($owner);

        // API nahi: sirf WhatsApp link
        Livewire::test(TownOwnerDashboard::class)->assertSee('wa.me/923111234567', false)->assertDontSee('Sab ko reminder bhejein');

        \Illuminate\Support\Facades\Http::fake(['gw.test/*' => \Illuminate\Support\Facades\Http::response('ok')]);
        \App\Models\MessagingSetting::current()->update(['driver' => 'http', 'config' => ['url' => 'https://gw.test/send', 'payload' => '{"to":"{to}","text":"{message}"}']]);
        \App\Services\Messaging\Messenger::flush();

        Livewire::test(TownOwnerDashboard::class)
            ->assertSee('Sab ko reminder bhejein')
            ->call('sendAllOverdue')
            ->assertSee('2 paigham bhej diye gaye.');

        \Illuminate\Support\Facades\Http::assertSentCount(2);
        $logs = \App\Models\MessageLog::all();
        $this->assertSame([$owner->id], $logs->pluck('owner_id')->unique()->values()->all());
        $this->assertSame(['installment_overdue'], $logs->pluck('template_key')->unique()->values()->all());

        // Kisi aur owner ki qist id se bhejne ki koshish: kuch nahi jata
        \Illuminate\Support\Facades\Http::fake();
        $other = User::forceCreate(['name' => 'Other', 'email' => 'x@t.test', 'password' => bcrypt('x'), 'role' => 'town_owner', 'is_approved' => true]);
        $this->actingAs($other);
        Livewire::test(TownOwnerDashboard::class)->call('sendReminder', Installment::first()->id);
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_admin_messaging_settings_save_without_exposing_token(): void
    {
        $admin = User::forceCreate(['name' => 'Admin', 'email' => 'a@t.test', 'password' => bcrypt('x'), 'role' => 'admin', 'is_approved' => true]);
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\MessagingSettings::class)
            ->set('driver', 'http')->call('applyPreset', 'greenapi')
            ->set('config.url', 'https://api.green-api.com/waInstance1/sendMessage/{token}')
            ->set('token', 'TOPSECRET')
            ->set('templates.installment_due_soon.body', 'Salam {name}, qist {amount} {due_date} ko.')
            ->call('save')->assertHasNoErrors()
            ->assertSet('token', '')->assertSet('hasToken', true)
            ->assertDontSee('TOPSECRET');

        $s = \App\Models\MessagingSetting::current();
        $this->assertSame(['http', 'TOPSECRET'], [$s->driver, $s->get('token')]);
        $this->assertSame('Salam {name}, qist {amount} {due_date} ko.', \App\Models\MessageTemplate::where('key', 'installment_due_soon')->value('body'));
        $this->assertSame(1, \App\Models\MessageTemplate::count()); // baqi default hi rahe

        // Khaali token dobara save karne se purana mehfooz rehta hai; ghalat JSON rad
        Livewire::test(\App\Livewire\Admin\MessagingSettings::class)->call('save');
        $this->assertSame('TOPSECRET', \App\Models\MessagingSetting::current()->get('token'));
        Livewire::test(\App\Livewire\Admin\MessagingSettings::class)->set('config.payload', '{bad')->call('save')->assertHasErrors('config.payload');
    }

    public function test_team_member_only_sees_allowed_quick_actions(): void
    {
        $owner = $this->owner();
        $this->seedTown($owner);
        $staff = User::forceCreate(['name' => 'Junaid', 'email' => 's@t.test', 'password' => bcrypt('x'), 'role' => 'town_owner', 'is_approved' => true,
            'owner_id' => $owner->id, 'permissions' => ['dashboard', 'recovery']]);
        $this->actingAs($staff->fresh());

        Livewire::test(TownOwnerDashboard::class)
            ->assertSee('Qist wusooli')
            ->assertDontSee('Kharcha')
            ->assertDontSee('+ Nayi scheme')
            ->assertSee('Furqan Garden'); // owner ka data hi dikhe
    }
}
