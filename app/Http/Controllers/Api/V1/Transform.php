<?php

namespace App\Http\Controllers\Api\V1;

use App\Support\PlotPricing;
use App\Support\Tenant;
use Carbon\Carbon;

/**
 * One place that decides the JSON shape of every model the mobile app receives.
 * Money is returned as numbers (PKR), dates as "Y-m-d", times as ISO-8601.
 */
class Transform
{
    public static function date($value): ?string
    {
        return $value ? Carbon::parse($value)->toDateString() : null;
    }

    public static function time($value): ?string
    {
        return $value ? Carbon::parse($value)->toIso8601String() : null;
    }

    public static function user($u): array
    {
        $isTownOwner = $u->role === 'town_owner';

        return [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'role' => $u->role, // user | agent | town_owner | admin
            'is_approved' => (bool) $u->is_approved,
            'is_team_member' => $isTownOwner && (bool) $u->owner_id,
            'owner_id' => $u->owner_id ?: null,
            'permissions' => $isTownOwner
                ? ($u->owner_id ? array_values((array) $u->permissions) : array_merge(array_keys(Tenant::SECTIONS), ['team']))
                : [],
            'wallet_balance' => round((float) $u->wallet_balance, 2),
            'agency' => $u->role === 'agent' ? [
                'name' => $u->agency_name,
                'tagline' => $u->agency_tagline,
                'logo_url' => ApiController::mediaUrl($u->agency_logo),
                'username' => $u->username,
            ] : null,
            'created_at' => self::time($u->created_at),
        ];
    }

    public static function city($c): array
    {
        return ['id' => $c->id, 'name' => $c->name, 'province' => $c->province, 'is_featured' => (bool) $c->is_featured];
    }

    public static function property($p, bool $showContact = false): array
    {
        $images = array_values(array_filter((array) $p->images));

        return [
            'id' => $p->id,
            'title' => $p->title,
            'property_type' => $p->property_type,
            'purpose' => $p->purpose, // for_sale | for_rent
            'price' => round((float) $p->price, 2),
            'area_size' => $p->area_size,
            // properties also has a legacy 'city' text column that shadows the relation
            'city' => $p->relationLoaded('city') ? ($p->getRelation('city')?->name ?? $p->getAttributes()['city'] ?? null) : ($p->getAttributes()['city'] ?? null),
            'city_id' => $p->city_id,
            'town' => $p->relationLoaded('town') && $p->town ? ['id' => $p->town->id, 'name' => $p->town->name] : null,
            'location' => $p->location,
            'google_map_url' => $p->google_map_url,
            'description' => $p->description,
            'status' => $p->status,
            'is_featured' => (bool) $p->is_featured,
            'images' => array_map(fn ($i) => ApiController::mediaUrl($i), $images),
            'cover_image' => ApiController::mediaUrl($images[0] ?? null),
            'views_count' => (int) ($p->views_count ?? 0),
            'web_url' => route('property.details', $p->id),
            'agent' => $p->relationLoaded('agent') && $p->agent ? [
                'id' => $p->agent->id,
                'name' => $p->agent->name,
                'agency_name' => $p->agent->agency_name,
                // Contact only after a paid unlock (or for the listing's own agent)
                'phone' => $showContact ? $p->agent->phone : null,
                'email' => $showContact ? $p->agent->email : null,
            ] : null,
            'contact_unlocked' => $showContact,
            'updated_at' => self::time($p->updated_at),
        ];
    }

    public static function town($t, array $extra = []): array
    {
        $gallery = array_values(array_filter((array) $t->gallery_images));

        return array_merge([
            'id' => $t->id,
            'name' => $t->name,
            'city' => $t->getAttributes()['city'] ?? null,
            'city_id' => $t->city_id,
            'location' => $t->location,
            'total_area' => $t->total_area,
            'noc_number' => $t->noc_number,
            'google_map_url' => $t->google_map_url,
            'master_plan_url' => ApiController::mediaUrl($t->master_plan_map),
            'gallery' => array_map(fn ($i) => ApiController::mediaUrl($i), $gallery),
            'late_fee_percent' => (float) ($t->late_fee_percent ?? 0),
        ], $extra);
    }

    public static function plot($p, bool $withCustomer = false): array
    {
        return [
            'id' => $p->id,
            'town_id' => $p->town_id,
            'town_name' => $p->relationLoaded('town') ? $p->town?->name : null,
            'plot_number' => $p->plot_number,
            'block' => $p->block_name,
            'type' => $p->type,
            'size' => $p->size,
            'features' => PlotPricing::cleanFeatures($p->features),
            'feature_labels' => PlotPricing::featureLabels($p->features),
            'rate_per_marla' => $p->rate_per_marla !== null ? (float) $p->rate_per_marla : null,
            'total_price' => round((float) $p->total_price, 2),
            'down_payment' => round((float) $p->down_payment, 2),
            'total_installments' => (int) $p->total_installments,
            'status' => $p->status,
            'customer' => $withCustomer && $p->relationLoaded('customer') && $p->customer
                ? ['id' => $p->customer->id, 'name' => $p->customer->name, 'phone' => $p->customer->phone]
                : null,
            'booked_at' => self::date($p->booked_at),
        ];
    }

    public static function installment($i): array
    {
        $late = $i->status !== 'paid' && Carbon::parse($i->due_date)->lt(today());

        return [
            'id' => $i->id,
            'plot_id' => $i->plot_id,
            'booking_id' => $i->plot_booking_id,
            'number' => (int) $i->installment_number, // 0 = down payment
            'label' => (int) $i->installment_number === 0 ? 'Down Payment' : 'Installment #' . $i->installment_number,
            'amount' => round((float) $i->amount, 2),
            'late_fee' => round((float) ($i->late_fee ?? 0), 2),
            'due_date' => self::date($i->due_date),
            'paid_date' => self::date($i->paid_date),
            'status' => $i->status === 'paid' ? 'paid' : ($late ? 'overdue' : 'pending'),
            'days_late' => $late ? (int) Carbon::parse($i->due_date)->diffInDays(today()) : 0,
            'remarks' => $i->remarks,
            'received_by' => $i->relationLoaded('receivedBy') ? $i->receivedBy?->name : null,
            'receipt_url' => $i->status === 'paid' ? route('town_owner.receipt.print', $i->id) : null,
        ];
    }

    public static function customer($c, array $extra = []): array
    {
        return array_merge([
            'id' => $c->id,
            'name' => $c->name,
            'father_name' => $c->father_name,
            'cnic' => $c->cnic,
            'phone' => $c->phone,
            'address' => $c->address,
            'created_at' => self::time($c->created_at),
        ], $extra);
    }

    public static function booking($b): array
    {
        return [
            'id' => $b->id,
            'plot_id' => $b->plot_id,
            'allotment_number' => $b->allotmentNumber(),
            'status' => $b->status,
            'customer' => $b->relationLoaded('customer') && $b->customer ? self::customer($b->customer) : null,
            'booking_date' => self::date($b->booking_date),
            'total_price' => (float) $b->total_price,
            'discount' => (float) $b->discount,
            'final_price' => (float) $b->final_price,
            'down_payment' => (float) $b->down_payment,
            'installments_count' => (int) $b->installments_count,
            'installment_amount' => (float) $b->installment_amount,
            'interval_months' => (int) $b->interval_months,
            'first_due_date' => self::date($b->first_due_date),
            'portal_url' => $b->status === 'active' ? $b->portalUrl() : null,
            'allotment_letter_url' => route('town_owner.allotment.letter', $b->id),
            'cancelled_at' => self::date($b->cancelled_at),
            'refund_amount' => (float) $b->refund_amount,
        ];
    }

    public static function lead($l): array
    {
        return [
            'id' => $l->id,
            'client_name' => $l->client_name,
            'client_phone' => $l->client_phone,
            'client_email' => $l->client_email,
            'status' => $l->status, // new | contacted | site_visit | negotiation | closed_won | closed_lost
            'priority' => $l->priority, // hot | warm | cold
            'source' => $l->source,
            'budget' => $l->budget !== null ? (float) $l->budget : null,
            'town' => $l->relationLoaded('town') && $l->town ? ['id' => $l->town->id, 'name' => $l->town->name] : null,
            'plot' => $l->relationLoaded('plot') && $l->plot ? ['id' => $l->plot->id, 'plot_number' => $l->plot->plot_number] : null,
            'property' => $l->relationLoaded('property') && $l->property ? ['id' => $l->property->id, 'title' => $l->property->title] : null,
            'next_followup_at' => self::time($l->next_followup_at),
            'is_followup_late' => $l->next_followup_at && $l->next_followup_at->isPast() && ! in_array($l->status, ['closed_won', 'closed_lost'], true),
            'last_whatsapp_at' => self::time($l->last_whatsapp_at),
            'notes' => $l->notes,
            'created_at' => self::time($l->created_at),
        ];
    }

    public static function transaction($t): array
    {
        return [
            'id' => $t->id,
            'type' => $t->type, // deposit | unlock_fee | refund | adjustment
            'amount' => round((float) $t->amount, 2),
            'direction' => match (true) {
                $t->type === 'unlock_fee' => 'debit',
                $t->type === 'adjustment' && $t->admin_note === 'debit' => 'debit',
                default => 'credit',
            },
            'status' => $t->status,
            'description' => $t->description,
            'payment_method' => $t->payment_method,
            'admin_note' => $t->type === 'deposit' ? $t->admin_note : null,
            'created_at' => self::time($t->created_at),
        ];
    }
}
