<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'town_id',
        'title',
        'property_type',
        'purpose',
        'price',
        'area_size',
        'city',
        'location',
        'description',
        'features',
        'images',
        'status',
        'is_featured',
        'google_map_url'
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function town()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function town(): BelongsTo
    // {
    //     return $this->belongsTo(Town::class, 'town_id');
    // }


}