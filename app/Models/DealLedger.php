<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'property_id',
        'deal_title',
        'client_name',
        'client_phone',
        'deal_date',
        'deal_value',
        'total_commission',
        'received_amount',
        'pending_amount',
        'partner_name',
        'partner_share',
        'status',
        'notes',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}