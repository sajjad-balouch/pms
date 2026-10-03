<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Town;
use App\Models\Property;
use App\Models\User;
use App\Models\UnlockedContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FrontendHome extends Component
{
    // Property Filter Variables
    public $selectedTown = '';
    public $propertyType = ''; 
    public $purpose = '';      
    public $selectedAgent = '';
    public $size = '';
    public $filterStatus = 'all';

    // Agent Directory & Unlock Contact Variables
    public $agentRoleFilter = 'all'; 
    public $showUnlockModal = false;
    public $selectedAgentForUnlock = null;
    
    // Dynamic Wallet Fee State
    public $unlockFee = 0; 
    public $errorMessage = '';

    public function applyFilter()
    {
        $this->dispatch('scroll-to-results');
    }

    /**
     * Check if contact is unlocked & valid for current user
     */
    public function isUnlocked($agentId)
    {
        if (!Auth::check()) {
            return false;
        }

        return UnlockedContact::where('user_id', Auth::id())
            ->where('unlocked_user_id', $agentId)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }

    /**
     * Open Modal or Show details if already unlocked
     */
    public function openUnlockModal($agentId)
    {
        $this->errorMessage = '';

        // Agar pehle se unlocked hai aur 1 month poora nahi hua to direct dikhayen
        if ($this->isUnlocked($agentId)) {
            return;
        }

        $this->selectedAgentForUnlock = User::withCount('properties')->find($agentId);

        if ($this->selectedAgentForUnlock) {
            $settings = DB::table('system_settings')->first();

            if ($this->selectedAgentForUnlock->role === 'town_owner') {
                $this->unlockFee = $settings->lead_unlock_fee_town ?? 1000;
            } else {
                $this->unlockFee = $settings->lead_unlock_fee_agent ?? 500;
            }

            $this->showUnlockModal = true;
        }
    }

    /**
     * Confirm Contact Unlock with 1-Month Validity
     */
    public function confirmUnlock($agentId)
    {
        $this->errorMessage = '';

        if (!Auth::check()) {
            $this->errorMessage = 'Please login first to unlock contact details.';
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pehle se valid expiry check karein
        if ($this->isUnlocked($agentId)) {
            $this->showUnlockModal = false;
            return;
        }

        // Fetch Dynamic Fee
        $settings = DB::table('system_settings')->first();
        $agentToUnlock = User::find($agentId);

        if (!$agentToUnlock) {
            $this->errorMessage = 'Selected user not found.';
            return;
        }

        $requiredFee = ($agentToUnlock->role === 'town_owner') 
            ? ($settings->lead_unlock_fee_town ?? 1000) 
            : ($settings->lead_unlock_fee_agent ?? 500);

        // Balance Check
        if (($user->wallet_balance ?? 0) < $requiredFee) {
            $this->errorMessage = "Insufficient wallet balance! You need PKR " . number_format($requiredFee) . " to unlock.";
            return;
        }

        // Balance Deduct aur 1 Month Expiry ke sath Save karein
        DB::transaction(function () use ($user, $agentId, $requiredFee) {
            // Balance deduct karein
            $user->decrement('wallet_balance', $requiredFee);

            // DB Table update/insert karein mit 1 month validity
            UnlockedContact::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'unlocked_user_id' => $agentId,
                ],
                [
                    'expires_at' => Carbon::now()->addMonth(), // 1 Month validity
                ]
            );
        });

        $this->showUnlockModal = false;
        $this->selectedAgentForUnlock = null;

        session()->flash('success', 'Contact details unlocked for 1 month!');
    }

    public function render()
    {
        $towns = Town::all();

        $agentsQuery = User::whereIn('role', ['agent', 'town_owner'])
            ->withCount('properties');

        if ($this->agentRoleFilter !== 'all') {
            $agentsQuery->where('role', $this->agentRoleFilter);
        }

        $agents = $agentsQuery->latest()->get();

        $propertiesQuery = Property::with(['town', 'user']);

        if (!empty($this->selectedTown)) {
            $propertiesQuery->where('town_id', $this->selectedTown);
        }

        if (!empty($this->propertyType)) {
            $propertiesQuery->where('property_type', $this->propertyType);
        }

        if (!empty($this->purpose)) {
            $propertiesQuery->where('purpose', $this->purpose);
        }

        if (!empty($this->selectedAgent)) {
            $propertiesQuery->where('agent_id', $this->selectedAgent);
        }

        if (!empty($this->size)) {
            $propertiesQuery->where('area_size', $this->size);
        }

        if ($this->filterStatus !== 'all') {
            $propertiesQuery->where('status', $this->filterStatus);
        }

        $properties = $propertiesQuery->latest()->get();

        return view('livewire.frontend-home', [
            'towns'      => $towns,
            'agents'     => $agents,
            'properties' => $properties,
        ])->layout('layouts.front-app');
    }
}