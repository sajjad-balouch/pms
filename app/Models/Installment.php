<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'buyer_name',
        'buyer_phone',
        'installment_number',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'remarks',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
}