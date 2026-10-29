<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlotBooking extends Model
{
    protected $fillable = [
        'plot_id', 'customer_id', 'booking_date', 'total_price', 'discount', 'final_price',
        'down_payment', 'installments_count', 'installment_amount', 'interval_months', 'first_due_date',
        'status', 'portal_token', 'cancelled_at', 'cancel_reason', 'refund_amount', 'refund_date', 'remarks',
    ];

    protected $hidden = ['portal_token'];

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

    public static function newPortalToken(): string
    {
        return Str::random(40);
    }

    // Buyer portal link (token is created the first time it is needed)
    public function portalUrl(): ?string
    {
        if ($this->status !== 'active') {
            return null;
        }

        if (! $this->portal_token) {
            $this->forceFill(['portal_token' => self::newPortalToken()])->save();
        }

        return route('portal.show', $this->portal_token);
    }

    // e.g. AL-007-00042
    public function allotmentNumber(): string
    {
        return 'AL-' . str_pad((string) ($this->plot?->town_id ?? 0), 3, '0', STR_PAD_LEFT)
            . '-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    // Public verification (QR on the allotment letter). Shows only limited info, never the payment ledger.
    public function verifyHash(): string
    {
        return substr(hash_hmac('sha256', 'booking-verify:' . $this->id, (string) config('app.key')), 0, 24);
    }

    public function verifyUrl(): string
    {
        return route('portal.verify', ['booking' => $this->id, 'hash' => $this->verifyHash()]);
    }
}
