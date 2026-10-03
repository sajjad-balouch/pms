<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'city',
        'total_area',
        'noc_number',
        'is_active',
        'google_map_url'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

    // Town belong karta hai User (Owner) se
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}