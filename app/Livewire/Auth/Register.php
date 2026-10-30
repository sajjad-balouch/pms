<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|lowercase|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('required|in:user,town_owner,agent')]
    public string $role = 'user'; // Default Role

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    // Explicit Role Setter for UI Radio Cards
    public function setRole(string $selectedRole): void
    {
        if (in_array($selectedRole, ['user', 'town_owner', 'agent'])) {
            $this->role = $selectedRole;
        }
    }

    public function register()
    {
        $this->validate();

        $isUser = $this->role === 'user';

        // Admin setting: town owners / agents may need approval before they can log in.
        // (Pehle message "pending approval" dikhata tha lekin account foran approved hota tha.)
        $needsApproval = ! $isUser && (bool) \App\Models\SystemSetting::getSettings()->require_provider_approval;

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'trial_ends_at' => $isUser ? Carbon::now()->addDays(3) : null,
            'is_approved' => ! $needsApproval,
        ]);

        event(new \Illuminate\Auth\Events\Registered($user));

        if (! $isUser) {
            session()->flash('status', $needsApproval
                ? __('Account registered successfully! Pending admin approval.')
                : __('Account registered successfully! You can log in now.'));
            return redirect()->route('login');
        }

        auth()->login($user);

        return redirect()->route('user.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.front-app');
    }
}