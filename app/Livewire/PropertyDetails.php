<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\User;
use App\Services\LeadCapture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PropertyDetails extends Component
{
    const UNLOCK_FEE = 500;

    public $property;

    #[Locked]
    public $propertyId;

    public $isUnlocked = false;

    // Locked: pehle browser se fee 0 ya minus karke muft unlock / wallet mein paise barhaye ja sakte the
    #[Locked]
    public $unlockFee = self::UNLOCK_FEE;

    // Fee set by admin (Admin Dashboard > Platform Fee); UNLOCK_FEE is only the fallback
    public static function currentFee(): float
    {
        return (float) (\App\Models\SystemSetting::getSettings()->lead_unlock_fee_agent ?? self::UNLOCK_FEE);
    }

    public function mount($id)
    {
        $this->propertyId = $id;
        $this->unlockFee = self::currentFee(); // shown in the modal; the charge itself is re-read on the server
        $this->loadPropertyData();
        $this->countView();
    }

    // Listing analytics: ek session mein ek property ka ek view (agent khud ginti mein nahi)
    protected function countView(): void
    {
        $key = 'viewed_property_' . $this->property->id;

        if (session()->has($key) || Auth::id() === $this->property->agent_id) {
            return;
        }

        session()->put($key, true);
        Property::whereKey($this->property->id)->update([
            'views_count' => DB::raw('views_count + 1'),
            'last_viewed_at' => now(),
        ]);
    }

    public function loadPropertyData()
    {
        $this->property = Property::with(['agent', 'city', 'town.city'])->findOrFail($this->propertyId);

        if (Auth::check()) {
            $hasUnlocked = DB::table('property_unlocks')
                ->where('user_id', Auth::id())
                ->where('property_id', $this->property->id)
                ->exists();

            if ($hasUnlocked || Auth::id() === $this->property->agent_id) {
                $this->isUnlocked = true;
            }
        }
    }

    public function processUnlock()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $fee = self::currentFee(); // always from the server, never from the browser
        $propertyId = $this->property->id;

        try {
            $result = DB::transaction(function () use ($fee, $propertyId) {
                // Lock the wallet row: do tez clicks se balance minus mein na jaye
                $user = User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();

                // Pehle se unlock hai to dobara paise na katein
                if (DB::table('property_unlocks')->where('user_id', $user->id)->where('property_id', $propertyId)->exists()) {
                    return 'already';
                }

                if ((float) ($user->wallet_balance ?? 0) < $fee) {
                    return 'insufficient';
                }

                $user->decrement('wallet_balance', $fee);

                DB::table('property_unlocks')->insert([
                    'user_id' => $user->id,
                    'property_id' => $propertyId,
                    'amount_paid' => $fee,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('wallet_transactions')->insert([
                    'user_id' => $user->id,
                    'amount' => $fee,
                    'type' => 'unlock_fee',
                    'description' => 'Unlocked contact details for property #' . $propertyId,
                    'reference_id' => (string) $propertyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Agent ke CRM mein garam lead
                LeadCapture::fromUnlock($this->property->agent_id, $user, [
                    'property_id' => $propertyId,
                    'town_id' => $this->property->town_id,
                    'title' => $this->property->title . ' (PKR ' . number_format((float) $this->property->price) . ')',
                ]);

                return 'unlocked';
            });
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', __('Transaction error, please try again.'));
            return;
        }

        if ($result === 'insufficient') {
            session()->flash('error', __('Insufficient wallet balance. Please top up your wallet in dashboard.'));
            return;
        }

        $this->isUnlocked = true;
        $this->dispatch('unlock-success'); // Alpine modal ko band karne ke liye event
        session()->flash('success', $result === 'already' ? __('This contact is already unlocked.') : __('Contact details unlocked successfully!'));
    }

    public function render()
    {
        return view('livewire.property-details')
            ->layout('layouts.front-app');
    }
}