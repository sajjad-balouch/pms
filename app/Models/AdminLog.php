<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdminLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'target_type', 'target_id', 'summary', 'details', 'ip_address'];

    protected $casts = [
        'details' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // AdminLog::record('topup.approved', $transaction, 'Approved Rs. 5,000 for Ali', ['amount' => 5000])
    public static function record(string $action, $target = null, ?string $summary = null, array $details = []): self
    {
        return self::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'target_type' => $target ? class_basename($target) : null,
            'target_id' => $target?->getKey(),
            'summary' => $summary ? mb_substr($summary, 0, 500) : null,
            'details' => $details ?: null,
            'ip_address' => request()->ip(),
        ]);
    }
}
