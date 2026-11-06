<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Platform ki paighamat settings (ek hi row). Token waghera encrypted rehte hain. */
class MessagingSetting extends Model
{
    protected $fillable = ['driver', 'config', 'owner_daily_limit'];

    protected $casts = [
        'config' => 'encrypted:array',
        'owner_daily_limit' => 'integer',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([], ['driver' => 'link', 'config' => [], 'owner_daily_limit' => 200]);
    }

    public function get(string $key, $default = null)
    {
        return ($this->config ?? [])[$key] ?? $default;
    }
}
