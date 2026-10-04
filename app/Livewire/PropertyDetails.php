<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PropertyDetails extends Component
{
    public $property;
    public $propertyId;
    public $isUnlocked = false;
    public $unlockFee = 500;

    public function mount($id)
    {
        $this->propertyId = $id;
        $this->loadPropertyData();
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

        $user = User::find(Auth::id());

        if (($user->wallet_balance ?? 0) < $this->unlockFee) {
            session()->flash('error', 'Insufficient wallet balance. Please top up your wallet in dashboard.');
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                // 1. Deduct balance from user
                $user->decrement('wallet_balance', $this->unlockFee);

                // 2. Record property unlock
                DB::table('property_unlocks')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'property_id' => $this->property->id,
                    ],
                    [
                        'amount_paid' => $this->unlockFee,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // 3. Record Wallet Transaction
                DB::table('wallet_transactions')->insert([
                    'user_id' => $user->id,
                    'amount' => $this->unlockFee,
                    'type' => 'unlock_fee',
                    'description' => 'Unlocked contact details for property #' . $this->property->id,
                    'reference_id' => (string) $this->property->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $this->isUnlocked = true;
            $this->dispatch('unlock-success'); // Alpine modal ko band karne ke liye event
            session()->flash('success', 'Contact details unlocked successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Transaction error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.property-details')
            ->layout('layouts.front-app');
    }
}