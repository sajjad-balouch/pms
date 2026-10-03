<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'agent_id',
        'town_id',
        'plot_id',
        'client_name',
        'client_phone',
        'client_email',
        'status',
        'budget',
        'notes',
    ];

    /**
     * Get the town/scheme associated with the lead.
     */
    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    /**
     * Get the plot associated with the lead.
     */
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    
}
