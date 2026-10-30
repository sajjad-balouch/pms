<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'payment_method',
        'sender_account_title',
        'sender_account_number',
        'screenshot',
        'status',
        'description',
        'reference_id',
        'admin_note',
        'approved_at',
        'rejected_at',
        'processed_by',
    ];

    // Admin who approved / rejected / adjusted
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}