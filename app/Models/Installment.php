<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'plot_booking_id',
        'buyer_name',
        'buyer_phone',
        'installment_number',
        'amount',
        'late_fee',
        'due_date',
        'paid_date',
        'received_by',
        'status',
        'remarks',
    ];

    // Team member (or owner) who recorded the payment
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
     public function booking()
    {
        return $this->belongsTo(PlotBooking::class, 'plot_booking_id');
    }
}