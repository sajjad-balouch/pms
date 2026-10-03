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
        'google_map_url',
        'city_id',
        'slug',
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

    /**
     * Get all properties belonging to this town/scheme.
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'town_id'); 
    }

    /**
     * Town belongs to a City.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

}