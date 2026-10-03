<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_unlock_fee_agent',
        'lead_unlock_fee_town',
        'town_owner_monthly_fee',
        'agent_monthly_fee',
        'user_free_trial_days',
        'provider_free_months',
    ];

    // Static helper to get or create single settings row
    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'lead_unlock_fee_agent' => 50.00,
            'lead_unlock_fee_town' => 100.00,
            'town_owner_monthly_fee' => 5000.00,
            'agent_monthly_fee' => 2000.00,
            'user_free_trial_days' => 3,
            'provider_free_months' => 1,
        ]);
    }
}