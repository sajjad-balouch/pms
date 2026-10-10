<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'page_title',
        'ip_address',
        'city',
        'region',
        'country',
        'device',
        'browser',
        'view_date',
        'viewed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}