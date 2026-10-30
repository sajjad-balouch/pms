<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Transform;
use App\Models\DealLedger;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentDashboardController extends ApiController
{
    const OPEN = ['new', 'contacted', 'site_visit', 'negotiation'];

    public function show(Request $request)
    {
        $agentId = $request->user()->id;
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $leads = Lead::where('agent_id', $agentId);

        $byStatus = (clone $leads)->selectRaw('status, COUNT(*) AS n, COALESCE(SUM(budget), 0) AS value')->groupBy('status')->get()->keyBy('status');
        $won = (int) ($byStatus['closed_won']->n ?? 0);
        $lost = (int) ($byStatus['closed_lost']->n ?? 0);

        $commission = DealLedger::where('agent_id', $agentId)
            ->selectRaw('COALESCE(SUM(total_commission), 0) AS earned, COALESCE(SUM(received_amount), 0) AS received, COALESCE(SUM(pending_amount), 0) AS pending')
            ->first();

        $listings = Property::where('agent_id', $agentId);

        return $this->ok([
            'leads' => [
                'active' => (int) collect(self::OPEN)->sum(fn ($s) => $byStatus[$s]->n ?? 0),
                'hot' => (clone $leads)->whereIn('status', self::OPEN)->where('priority', 'hot')->count(),
                'new_this_week' => (clone $leads)->where('created_at', '>=', $now->copy()->subDays(7))->count(),
                'portal_this_week' => (clone $leads)->where('source', 'portal')->where('created_at', '>=', $now->copy()->subDays(7))->count(),
                'won' => $won,
                'conversion_percent' => ($won + $lost) > 0 ? round($won / ($won + $lost) * 100) : 0,
                'pipeline_value' => (float) collect(['site_visit', 'negotiation'])->sum(fn ($s) => $byStatus[$s]->value ?? 0),
                'funnel' => collect(self::OPEN)->map(fn ($s) => ['status' => $s, 'count' => (int) ($byStatus[$s]->n ?? 0), 'value' => (float) ($byStatus[$s]->value ?? 0)]),
            ],
            'followups' => [
                'late' => (clone $leads)->whereIn('status', self::OPEN)->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now)->count(),
                'today' => (clone $leads)->whereIn('status', self::OPEN)->whereBetween('next_followup_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()])->count(),
                'due' => (clone $leads)->with(['town:id,name', 'plot:id,plot_number', 'property:id,title'])
                    ->whereIn('status', self::OPEN)->whereNotNull('next_followup_at')
                    ->where('next_followup_at', '<=', $now->copy()->endOfDay())
                    ->orderBy('next_followup_at')->limit(10)->get()
                    ->map(fn ($l) => Transform::lead($l)),
            ],
            'portal_leads' => (clone $leads)->with(['property:id,title', 'town:id,name'])
                ->where('source', 'portal')->where('status', 'new')->latest()->limit(5)->get()
                ->map(fn ($l) => Transform::lead($l)),
            'commission' => [
                'earned' => (float) $commission->earned,
                'received' => (float) $commission->received,
                'pending' => (float) $commission->pending,
                'this_month' => (float) DealLedger::where('agent_id', $agentId)->where('deal_date', '>=', $monthStart->toDateString())->sum('total_commission'),
            ],
            'listings' => [
                'active' => (clone $listings)->where('status', 'available')->count(),
                'total' => (clone $listings)->count(),
                'views' => (int) (clone $listings)->sum('views_count'),
                'stale' => (clone $listings)->where('status', 'available')->where('updated_at', '<', $now->copy()->subDays(30))->count(),
                'unlocks_this_month' => DB::table('property_unlocks')->whereIn('property_id', (clone $listings)->select('id'))->where('created_at', '>=', $monthStart)->count(),
            ],
        ]);
    }
}
