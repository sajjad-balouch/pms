<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnlockedContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unlocked_user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function unlockedUser()
    {
        return $this->belongsTo(User::class, 'unlocked_user_id');
    }
}