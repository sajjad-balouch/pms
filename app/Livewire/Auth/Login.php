<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        // dd('uff');
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt authentication
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'دیے گئے کریڈینشلز غلط ہیں یا ریکارڈ میں نہیں ہیں۔');
            return;
        }

        $user = Auth::user();

        // Check if Town Owner or Agent is approved by Admin
        if (in_array($user->role, ['town_owner', 'agent']) && ! $user->is_approved) {
            Auth::logout();
            session()->flash('status', 'آپ کا اکاؤنٹ ایڈمن کی منظوری (Approval) کا منتظر ہے۔ برائے مہربانی انتظار کریں۔');
            return redirect()->route('login');
        }

        session()->regenerate();

        // Explicit Direct Redirect based on Role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'town_owner') {
            return redirect()->route('town_owner.dashboard');
        } elseif ($user->role === 'agent') {
            return redirect()->route('agent.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.front-app');
    }
}