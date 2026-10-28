<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotTransfer extends Model
{
    protected $fillable = [
        'plot_id', 'plot_booking_id', 'from_customer_id', 'to_customer_id', 'transfer_date', 'transfer_fee', 'remarks',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function booking()
    {
        return $this->belongsTo(PlotBooking::class, 'plot_booking_id');
    }

    public function fromCustomer()
    {
        return $this->belongsTo(Customer::class, 'from_customer_id');
    }

    public function toCustomer()
    {
        return $this->belongsTo(Customer::class, 'to_customer_id');
    }
}
