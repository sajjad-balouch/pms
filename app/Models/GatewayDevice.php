<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Platform ka apna Android phone jo SIM se SMS bhejta hai */
class GatewayDevice extends Model
{
    protected $fillable = ['name', 'token_hash', 'sim_number', 'is_active', 'daily_limit', 'last_seen_at', 'last_status'];

    protected $hidden = ['token_hash'];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_limit' => 'integer',
        'last_seen_at' => 'datetime',
        'last_status' => 'array',
    ];

    /** Naya phone; asal token sirf ek dafa wapas milta hai (admin ko dikhane ke liye) */
    public static function register(string $name, ?string $simNumber = null, int $dailyLimit = 300): array
    {
        $token = 'gw_' . Str::random(40);
        $device = self::create([
            'name' => $name,
            'sim_number' => $simNumber ?: null,
            'daily_limit' => $dailyLimit,
            'token_hash' => hash('sha256', $token),
        ]);

        return [$device, $token];
    }

    public static function findByToken(?string $token): ?self
    {
        return $token ? self::where('token_hash', hash('sha256', $token))->first() : null;
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(3));
    }

    public function sentToday(): int
    {
        return MessageLog::where('device_id', $this->id)->whereIn('status', ['sent', 'sending'])
            ->where('updated_at', '>=', now()->startOfDay())->count();
    }
}
