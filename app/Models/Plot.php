<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    use HasFactory;

    protected $fillable = [
        'town_id', 'customer_id', 'plot_number', 'block_name', 'type', 'size',
        'rate_per_marla', 'base_price', 'features',
        'total_price', 'down_payment', 'total_installments', 'status', 'booked_at', 'sold_at',
    ];

    protected $casts = [
        'features' => 'array',
        'booked_at' => 'date',
        'sold_at' => 'date',
    ];

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function bookings()
    {
        return $this->hasMany(PlotBooking::class);
    }

    public function activeBooking()
    {
        return $this->hasOne(PlotBooking::class)->where('status', 'active')->latestOfMany();
    }

    public function transfers()
    {
        return $this->hasMany(PlotTransfer::class);
    }


    // Jis User/Admin ne post kiya uski Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Direct owner nikalne ke liye 'hasOneThrough' (Town ke zariye User)
    public function owner()
    {
        return $this->hasOneThrough(
            User::class,
            Town::class,
            'id',       // Town table ki Primary Key
            'id',       // User table ki Primary Key
            'town_id',  // Plot table me Foreign Key
            'user_id'   // Town table me Foreign Key (Owner ki ID)
        );
    }

    
    
}