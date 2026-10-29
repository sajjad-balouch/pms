<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotFollowup extends Model
{
    const CHANNELS = [
        'call' => '📞 Call',
        'whatsapp' => '💬 WhatsApp',
        'visit' => '🚶 Visit',
        'sms' => '✉️ SMS',
    ];

    const OUTCOMES = [
        'reached' => 'Baat hui',
        'promised' => 'Adayegi ka wada',
        'no_answer' => 'Jawab nahi mila',
        'refused' => 'Inkar / jhagra',
        'sent' => 'Reminder bheja',
    ];

    protected $fillable = ['plot_id', 'plot_booking_id', 'user_id', 'channel', 'outcome', 'promise_date', 'note'];

    protected $casts = [
        'promise_date' => 'date',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
