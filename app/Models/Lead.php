<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    protected $casts = [
        'next_followup_at' => 'datetime',
        'last_whatsapp_at' => 'datetime',
        'budget'           => 'decimal:2',
    ];
    protected $fillable = [
        'agent_id',
        'town_id',
        'plot_id',
        'property_id',
        'client_name',
        'client_phone',
        'client_email',
        'status',
        'priority',          // hot, warm, cold
        'source',            // portal, whatsapp, call, walk_in, social_media, banner
        'budget',
        'next_followup_at',  // Scheduled call/visit date & time
        'last_whatsapp_at',  // Last WhatsApp outreach timestamp
        'notes',
    ];

    // Listing jis se lead aayi (portal contact unlock)
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

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
