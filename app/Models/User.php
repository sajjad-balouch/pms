<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'trial_ends_at',
        'wallet_balance',
        'is_approved',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trial_ends_at' => 'datetime',
            'is_approved' => 'boolean',
        ];
    }

    // --- Helper Methods for Roles ---
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTownOwner(): bool
    {
        return $this->role === 'town_owner';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // Check if 3-Day Free Trial is Active (For Regular Users)
    public function hasActiveTrial(): bool
    {
        return $this->trial_ends_at && Carbon::now()->lt($this->trial_ends_at);
    }


    /**
     * Get all properties listed by the user/agent/town owner.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

}