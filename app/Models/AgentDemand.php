<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentDemand extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'title',
        'property_type',
        'city',
        'target_location',
        'area_size',
        'budget_min',
        'budget_max',
        'urgency',
        'details',
        'status',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}