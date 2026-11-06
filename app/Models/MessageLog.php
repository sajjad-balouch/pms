<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Har bheja gaya (ya nakaam) paigham. Apne SMS gateway ke liye yahi queue bhi hai:
 * queued -> sending (phone ne utha liya) -> sent / failed. Nakaam par wallet mein paise wapas.
 */
class MessageLog extends Model
{
    protected $fillable = ['owner_id', 'user_id', 'template_key', 'to', 'driver', 'device_id', 'body', 'segments', 'cost', 'refunded', 'attempts', 'claimed_at', 'sent_at', 'status', 'provider_id', 'error', 'context'];

    protected $casts = [
        'context' => 'array',
        'cost' => 'decimal:2',
        'refunded' => 'boolean',
        'claimed_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(GatewayDevice::class, 'device_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
