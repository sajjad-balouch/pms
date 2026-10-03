<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PropertyDetails extends Component
{
    public $property;
    public $isUnlocked = false;
    public $unlockFee = 500;
    public $showModal = false;

    public function mount($id)
    {
        $this->property = Property::with(['agent', 'town'])->findOrFail($id);

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

    public function openUnlockModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->showModal = true;
    }

    public function processUnlock()
    {
        $user = Auth::user();

        if (($user->wallet_balance ?? 0) < $this->unlockFee) {
            session()->flash('error', 'Insufficient wallet balance. Please top up your wallet in dashboard.');
            return;
        }

        DB::transaction(function () use ($user) {
            // 1. Deduct balance from user
            $user->decrement('wallet_balance', $this->unlockFee);

            // 2. Record property unlock
            DB::table('property_unlocks')->insert([
                'user_id' => $user->id,
                'property_id' => $this->property->id,
                'amount_paid' => $this->unlockFee,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
        $this->showModal = false;
        session()->flash('success', 'Contact details unlocked successfully!');
    }

    public function render()
    {
        return view('livewire.property-details')
            ->layout('layouts.front-app');
    }
}