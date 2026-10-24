<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistryTaxEstimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_uuid',
        'user_id',
        'city',
        'property_type',
        'area_unit',
        'area_size',
        'rate_per_unit',
        'total_declared_value',
        'buyer_is_filer',
        'seller_is_filer',
        'calculated_breakdown',
        'grand_total_taxes',
        'ip_address',
    ];

    protected $casts = [
        'calculated_breakdown' => 'array',
        'buyer_is_filer' => 'boolean',
        'seller_is_filer' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}