<?php

namespace App\Livewire\Agent;

use App\Models\DealLedger;
use App\Models\Lead;
use App\Models\Property;
use App\Support\WhatsApp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AgentDashboard extends Component
{
    const OPEN = ['new', 'contacted', 'site_visit', 'negotiation'];
    const STALE_DAYS = 30;

    // Follow-up done from the dashboard: mark contacted + next follow-up
    public function snooze($leadId, $days = 1)
    {
        $lead = Lead::where('agent_id', Auth::id())->findOrFail($leadId);
        $days = in_array((int) $days, [1, 3, 7], true) ? (int) $days : 1;

        $lead->update([
            'status' => $lead->status === 'new' ? 'contacted' : $lead->status,
            'next_followup_at' => now()->addDays($days)->setTime(11, 0),
        ]);
    }

    public function render()
    {
        $agentId = Auth::id();
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        $leads = Lead::where('agent_id', $agentId);

        // Pipeline
        $byStatus = (clone $leads)->selectRaw('status, COUNT(*) AS n, COALESCE(SUM(budget), 0) AS value')->groupBy('status')->get()->keyBy('status');
        $won = (int) ($byStatus['closed_won']->n ?? 0);
        $lost = (int) ($byStatus['closed_lost']->n ?? 0);

        $stats = [
            'total' => (int) $byStatus->sum('n'),
            'active' => (int) collect(self::OPEN)->sum(fn ($s) => $byStatus[$s]->n ?? 0),
            'hot' => (clone $leads)->whereIn('status', self::OPEN)->where('priority', 'hot')->count(),
            'won' => $won,
            'conversion' => ($won + $lost) > 0 ? round($won / ($won + $lost) * 100) : 0,
            'pipeline_value' => (float) collect(['site_visit', 'negotiation'])->sum(fn ($s) => $byStatus[$s]->value ?? 0),
            'new_week' => (clone $leads)->where('created_at', '>=', $now->copy()->subDays(7))->count(),
            'portal_week' => (clone $leads)->where('source', 'portal')->where('created_at', '>=', $now->copy()->subDays(7))->count(),
        ];

        $funnel = collect(self::OPEN)->map(fn ($s) => [
            'status' => $s,
            'count' => (int) ($byStatus[$s]->n ?? 0),
            'value' => (float) ($byStatus[$s]->value ?? 0),
        ])->all();

        // Follow-ups: late + today (open leads only)
        $followups = (clone $leads)->with(['town:id,name', 'plot:id,plot_number'])
            ->whereIn('status', self::OPEN)
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', $now->copy()->endOfDay())
            ->orderBy('next_followup_at')
            ->limit(10)
            ->get()
            ->each(function ($l) {
                $l->whatsapp = WhatsApp::url($l->client_phone, __('Assalam o Alaikum :client, this is :agent from AssanZameen regarding your property inquiry.', [
                    'client' => $l->client_name,
                    'agent' => Auth::user()->name,
                ]));
            });

        $lateCount = (clone $leads)->whereIn('status', self::OPEN)->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now)->count();
        $todayCount = (clone $leads)->whereIn('status', self::OPEN)->whereBetween('next_followup_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()])->count();
        $noFollowup = (clone $leads)->whereIn('status', self::OPEN)->whereNull('next_followup_at')->count();

        // Fresh portal leads (paid unlocks) — call within the hour
        $portalLeads = (clone $leads)->with('property:id,title')
            ->where('source', 'portal')
            ->where('status', 'new')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function ($l) {
                $l->whatsapp = WhatsApp::url($l->client_phone, __('Assalam o Alaikum :client, you showed interest in :property on AssanZameen. I am :agent — shall I send you photos and full details?', [
                    'client' => $l->client_name,
                    'property' => $l->property?->title ?? __('a property'),
                    'agent' => Auth::user()->name,
                ]));
            });

        // Commission
        $commission = DealLedger::where('agent_id', $agentId)
            ->selectRaw('COALESCE(SUM(total_commission), 0) AS earned, COALESCE(SUM(received_amount), 0) AS received, COALESCE(SUM(pending_amount), 0) AS pending')
            ->first();
        $commissionMonth = (float) DealLedger::where('agent_id', $agentId)->where('deal_date', '>=', $monthStart->toDateString())->sum('total_commission');

        // Listings
        $listings = Property::where('agent_id', $agentId);
        $listingStats = [
            'active' => (clone $listings)->where('status', 'available')->count(),
            'total' => (clone $listings)->count(),
            'views' => (int) (clone $listings)->sum('views_count'),
            'stale' => (clone $listings)->where('status', 'available')->where('updated_at', '<', $now->copy()->subDays(self::STALE_DAYS))->count(),
            'unlocks_month' => DB::table('property_unlocks')
                ->whereIn('property_id', (clone $listings)->select('id'))
                ->where('created_at', '>=', $monthStart)
                ->count(),
        ];

        $topListings = (clone $listings)->where('status', 'available')
            ->orderByDesc('views_count')
            ->limit(3)
            ->get(['id', 'title', 'views_count', 'price']);

        // Daily summary for WhatsApp
        $summary = implode("\n", [
            '📊 *' . __("Today's Summary") . '* — ' . $now->translatedFormat('d M Y'),
            '📞 ' . __('Follow-ups: :today today, :late late', ['today' => $todayCount, 'late' => $lateCount]),
            '🔥 ' . __('Hot leads: :hot · New (7 days): :new', ['hot' => $stats['hot'], 'new' => $stats['new_week']]),
            '💼 ' . __('Pipeline: Rs. :amount', ['amount' => number_format($stats['pipeline_value'])]),
            '💰 ' . __('Commission pending: Rs. :amount', ['amount' => number_format((float) $commission->pending)]),
            '🏠 ' . __('Active listings: :active · Unlocks this month: :unlocks', ['active' => $listingStats['active'], 'unlocks' => $listingStats['unlocks_month']]),
        ]);

        return view('livewire.agent.agent-dashboard', [
            'stats' => $stats,
            'funnel' => $funnel,
            'followups' => $followups,
            'lateCount' => $lateCount,
            'todayCount' => $todayCount,
            'noFollowup' => $noFollowup,
            'portalLeads' => $portalLeads,
            'commission' => $commission,
            'commissionMonth' => $commissionMonth,
            'listingStats' => $listingStats,
            'topListings' => $topListings,
            'summaryToSelf' => WhatsApp::url(Auth::user()->phone, $summary),
            'recentLeads' => (clone $leads)->with(['town', 'plot'])->latest()->take(5)->get(),
        ])->layout('layouts.app');
    }
}
