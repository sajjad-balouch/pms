<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'province',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Auto generate slug on create/update
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($city) {
            if (empty($city->slug) || $city->isDirty('name')) {
                $city->slug = Str::slug($city->name);
            }
        });
    }

    /* ================= Scopes ================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /* ================= Relationships ================= */

    // Example relationships for real estate platform
    public function societies()
    {
        return $table->hasMany(Society::class); // or Town
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}