<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BayanaDeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'deed_uuid',
        'user_id',
        'seller_name',
        'seller_father',
        'seller_cnic',
        'seller_phone',
        'seller_address',
        'buyer_name',
        'buyer_father',
        'buyer_cnic',
        'buyer_phone',
        'buyer_address',
        'property_title',
        'city',
        'location_mouza',
        'khasra_khewat',
        'area_size',
        'total_amount',
        'bayana_advance_amount',
        'remaining_amount',
        'payment_method',
        'payment_reference',
        'deed_date',
        'target_registry_date',
        'witness1_name',
        'witness1_cnic',
        'witness2_name',
        'witness2_cnic',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}