<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'name', 'father_name', 'cnic', 'phone', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

    public function bookings()
    {
        return $this->hasMany(PlotBooking::class);
    }
}
