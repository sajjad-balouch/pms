<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'location',
        'city',
        'google_map_url',
        'master_plan_map',
        'gallery_images',
        'total_area',
        'noc_number',
    ];

    protected $casts = [
        'gallery_images' => 'array',
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