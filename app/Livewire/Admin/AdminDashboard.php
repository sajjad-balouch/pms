<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $lead_unlock_fee_agent;
    public $lead_unlock_fee_town;
    public $town_owner_monthly_fee;
    public $agent_monthly_fee;

    public function mount()
    {
        $settings = SystemSetting::getSettings();
        $this->lead_unlock_fee_agent = $settings->lead_unlock_fee_agent;
        $this->lead_unlock_fee_town = $settings->lead_unlock_fee_town;
        $this->town_owner_monthly_fee = $settings->town_owner_monthly_fee;
        $this->agent_monthly_fee = $settings->agent_monthly_fee;
    }

    // Toggle Approval Status for Agents or Town Owners
    public function toggleApproval($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_approved = ! $user->is_approved;
        $user->save();

        session()->flash('status', "User {$user->name} approval status updated.");
    }

    // Save Fee & Subscription Settings
    public function updateSettings()
    {
        $this->validate([
            'lead_unlock_fee_agent' => 'required|numeric|min:0',
            'lead_unlock_fee_town' => 'required|numeric|min:0',
            'town_owner_monthly_fee' => 'required|numeric|min:0',
            'agent_monthly_fee' => 'required|numeric|min:0',
        ]);

        $settings = SystemSetting::getSettings();
        $settings->update([
            'lead_unlock_fee_agent' => $this->lead_unlock_fee_agent,
            'lead_unlock_fee_town' => $this->lead_unlock_fee_town,
            'town_owner_monthly_fee' => $this->town_owner_monthly_fee,
            'agent_monthly_fee' => $this->agent_monthly_fee,
        ]);

        session()->flash('settings_status', 'System fee settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => User::where('role', 'user')->count(),
            'totalTownOwners' => User::where('role', 'town_owner')->count(),
            'totalAgents' => User::where('role', 'agent')->count(),
            'pendingApprovals' => User::whereIn('role', ['town_owner', 'agent'])->where('is_approved', false)->get(),
            'allProviders' => User::whereIn('role', ['town_owner', 'agent'])->latest()->paginate(10),
        ])->layout('layouts.admin-app'); // App layout ki jagah temporary Guest layout use kar rahe hain
    }
}