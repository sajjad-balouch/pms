<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Models\Installment;
use App\Models\PlotBooking;
use App\Models\PlotFollowup;
use App\Services\BookingService;
use App\Support\WhatsApp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// Daily recovery list: late, due today, next 7 days, promises to pay
class RecoveryController extends OwnerController
{
    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), ['overdue', 'today', 'week', 'promises'], true) ? $request->query('tab') : 'overdue';
        $todayStr = today()->toDateString();
        $weekEnd = today()->addDays(7)->toDateString();

        $plotIds = $this->ownerPlots()
            ->when($request->filled('town_id'), fn ($q) => $q->where('town_id', $request->integer('town_id')))
            ->select('plots.id');

        $unpaid = Installment::with(['plot:id,town_id,plot_number,block_name,customer_id', 'plot.town:id,name', 'plot.customer:id,name,phone'])
            ->whereIn('plot_id', $plotIds)
            ->whereIn('status', BookingService::UNPAID)
            ->where('due_date', '<=', $weekEnd)
            ->orderBy('due_date')
            ->limit(3000)
            ->get();

        $ids = $unpaid->pluck('plot_id')->unique();
        $bookings = PlotBooking::whereIn('plot_id', $ids)->where('status', 'active')->get(['id', 'plot_id', 'portal_token'])->keyBy('plot_id');
        $history = PlotFollowup::with('user:id,name')->whereIn('plot_id', $ids)->where('created_at', '>=', now()->subDays(120))->latest('id')->get()->groupBy('plot_id');

        $cases = $unpaid->groupBy('plot_id')->map(function ($items) use ($todayStr, $bookings, $history) {
            $plot = $items->first()->plot;
            $late = $items->filter(fn ($i) => (string) $i->due_date < $todayStr);
            $promise = $history->get($plot->id, collect())->first(fn ($f) => $f->promise_date !== null);
            $last = $history->get($plot->id, collect())->first();
            $name = $plot->customer?->name ?? $items->first()->buyer_name;
            $phone = $plot->customer?->phone ?? $items->first()->buyer_phone;
            $token = $bookings->get($plot->id)?->portal_token;

            $case = [
                'plot_id' => $plot->id,
                'plot_number' => $plot->plot_number,
                'block' => $plot->block_name,
                'town' => $plot->town?->name,
                'customer' => ['name' => $name, 'phone' => $phone],
                'late_amount' => (float) $late->sum('amount'),
                'late_count' => $late->count(),
                'oldest_due' => Transform::date($late->min('due_date')),
                'days_late' => $late->isNotEmpty() ? (int) Carbon::parse($late->min('due_date'))->diffInDays(today()) : 0,
                'today_amount' => (float) $items->filter(fn ($i) => (string) $i->due_date === $todayStr)->sum('amount'),
                'upcoming_amount' => (float) $items->filter(fn ($i) => (string) $i->due_date > $todayStr)->sum('amount'),
                'next_due' => Transform::date($items->filter(fn ($i) => (string) $i->due_date > $todayStr)->min('due_date')),
                'promise_date' => Transform::date($promise?->promise_date),
                'promise_broken' => $promise && $promise->promise_date->lt(today()),
                'last_followup' => $last ? [
                    'channel' => $last->channel, 'outcome' => $last->outcome, 'note' => $last->note,
                    'by' => $last->user?->name, 'at' => Transform::time($last->created_at),
                ] : null,
                'reminded_today' => $history->get($plot->id, collect())->contains(fn ($f) => $f->channel === 'whatsapp' && $f->created_at->isToday()),
            ];

            $portal = $token ? ' ' . __('Your account: :url', ['url' => route('portal.show', $token)]) : '';
            $case['whatsapp_url'] = WhatsApp::url($phone, $case['late_amount'] > 0
                ? __('Assalam o Alaikum :client, :place: :count installment(s) of Rs. :amount are late. Please pay soon.', ['client' => $name, 'place' => $plot->town?->name . ' — Plot #' . $plot->plot_number, 'count' => $case['late_count'], 'amount' => number_format($case['late_amount'])]) . $portal
                : __('Assalam o Alaikum :client, :place: reminder — your next installment of Rs. :amount is due soon.', ['client' => $name, 'place' => $plot->town?->name . ' — Plot #' . $plot->plot_number, 'amount' => number_format($case['today_amount'] ?: $case['upcoming_amount'])]) . $portal);

            return $case;
        });

        $list = match ($tab) {
            'today' => $cases->filter(fn ($c) => $c['today_amount'] > 0)->sortByDesc('today_amount'),
            'week' => $cases->filter(fn ($c) => $c['upcoming_amount'] > 0)->sortBy('next_due'),
            'promises' => $cases->filter(fn ($c) => $c['promise_date'])->sortBy('promise_date'),
            default => $cases->filter(fn ($c) => $c['late_amount'] > 0)->sortBy('oldest_due'),
        };

        return $this->ok($list->values(), null, 200, ['summary' => [
            'late_amount' => (float) $cases->sum('late_amount'),
            'late_customers' => $cases->where('late_amount', '>', 0)->count(),
            'today_amount' => (float) $cases->sum('today_amount'),
            'week_amount' => (float) $cases->sum('upcoming_amount'),
            'collected_today' => (float) Installment::whereIn('plot_id', $plotIds)->where('status', 'paid')->whereDate('paid_date', $todayStr)->sum('amount'),
        ]]);
    }

    public function followups($plot)
    {
        $plot = $this->ownedPlot($plot);

        return $this->ok(PlotFollowup::with('user:id,name')->where('plot_id', $plot->id)->latest('id')->limit(50)->get()->map(fn ($f) => [
            'id' => $f->id,
            'channel' => $f->channel,
            'outcome' => $f->outcome,
            'promise_date' => Transform::date($f->promise_date),
            'note' => $f->note,
            'by' => $f->user?->name,
            'created_at' => Transform::time($f->created_at),
        ]));
    }

    public function storeFollowup(Request $request, $plot)
    {
        $plot = $this->ownedPlot($plot);

        $data = $request->validate([
            'channel' => ['required', Rule::in(array_keys(PlotFollowup::CHANNELS))],
            'outcome' => ['required', Rule::in(array_keys(PlotFollowup::OUTCOMES))],
            'promise_date' => 'required_if:outcome,promised|nullable|date|after_or_equal:today',
            'note' => 'nullable|string|max:500',
        ]);

        $f = PlotFollowup::create([
            'plot_id' => $plot->id,
            'plot_booking_id' => PlotBooking::where('plot_id', $plot->id)->where('status', 'active')->latest('id')->value('id'),
            'user_id' => Auth::id(),
            'channel' => $data['channel'],
            'outcome' => $data['outcome'],
            'promise_date' => $data['outcome'] === 'promised' ? $data['promise_date'] : null,
            'note' => $data['note'] ?? null,
        ]);

        return $this->created(['id' => $f->id], __('Follow-up saved.'));
    }
}
