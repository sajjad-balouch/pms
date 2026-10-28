<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotBooking extends Model
{
    protected $fillable = [
        'plot_id', 'customer_id', 'booking_date', 'total_price', 'discount', 'final_price',
        'down_payment', 'installments_count', 'installment_amount', 'interval_months', 'first_due_date',
        'status', 'cancelled_at', 'cancel_reason', 'refund_amount', 'refund_date', 'remarks',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'first_due_date' => 'date',
        'cancelled_at' => 'date',
        'refund_date' => 'date',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function transfers()
    {
        return $this->hasMany(PlotTransfer::class);
    }
}
