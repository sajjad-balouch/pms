<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;

/**
 * Buyer ne portal par kisi listing / plot ka contact unlock kiya => agent (ya town owner) ke CRM mein lead.
 * Same buyer pehle se open lead mein ho to nayi lead nahi banti, sirf note aur priority update hoti hai.
 */
class LeadCapture
{
    public static function fromUnlock(?int $agentId, User $buyer, array $context): ?Lead
    {
        if (! $agentId || (int) $agentId === (int) $buyer->id) {
            return null;
        }

        $phone = $buyer->phone ?: null;
        // Stored note is plain English data (the lead belongs to the agent, whatever language the buyer used)
        $note = '🔓 ' . now()->format('d M Y, h:i A') . ' — unlocked contact on portal: ' . ($context['title'] ?? 'listing');

        $existing = Lead::where('agent_id', $agentId)
            ->whereNotIn('status', ['closed_won', 'closed_lost'])
            ->where(function ($q) use ($phone, $buyer) {
                if ($phone) {
                    $q->where('client_phone', $phone);
                }
                if ($buyer->email) {
                    $q->orWhere('client_email', $buyer->email);
                }
            })
            ->latest('id')
            ->first();

        if ($existing) {
            $existing->update([
                'priority' => 'hot',
                'property_id' => $context['property_id'] ?? $existing->property_id,
                'plot_id' => $context['plot_id'] ?? $existing->plot_id,
                'town_id' => $context['town_id'] ?? $existing->town_id,
                'next_followup_at' => $existing->next_followup_at && $existing->next_followup_at->isFuture() ? $existing->next_followup_at : now()->addHour(),
                'notes' => trim($note . "\n" . $existing->notes),
            ]);

            return $existing;
        }

        return Lead::create([
            'agent_id' => $agentId,
            'property_id' => $context['property_id'] ?? null,
            'plot_id' => $context['plot_id'] ?? null,
            'town_id' => $context['town_id'] ?? null,
            'client_name' => $buyer->name ?: 'Portal Buyer',
            'client_phone' => $phone ?? ($buyer->email ?: 'N/A'),
            'client_email' => $buyer->email,
            'status' => 'new',
            'priority' => 'hot', // paise de kar contact khola — sab se garam lead
            'source' => 'portal',
            'budget' => $context['budget'] ?? null,
            'next_followup_at' => now()->addHour(),
            'notes' => $note,
        ]);
    }
}
