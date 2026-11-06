<?php

namespace App\Livewire\TownOwner;

use App\Support\Tenant;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\PlotFollowup;
use App\Models\Town;
use App\Services\BookingService;
use App\Services\Messaging\Messenger;
use App\Support\WhatsApp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Rozana ki wasooli ka page: late, aaj due, agle 7 din, aur "promise to pay".
 * Har customer (plot) ki ek line — WhatsApp reminder, call aur follow-up log.
 */
class RecoveryDesk extends Component
{
    const TABS = ['overdue', 'today', 'week', 'promises'];
    const LOOKAHEAD_DAYS = 7;
    const MAX_ROWS = 3000;

    #[Url(as: 'town', except: '')]
    public $townId = '';

    #[Url(except: 'overdue')]
    public $tab = 'overdue';

    #[Url(except: '')]
    public $search = '';

    // Follow-up modal
    public $fuPlotId = null;
    public $fu_channel = 'call';
    public $fu_outcome = 'reached';
    public $fu_promise_date = '';
    public $fu_note = '';

    protected $messages = [
        'fu_promise_date.required_if' => 'Wade ki tareekh likhein.',
        'fu_promise_date.after_or_equal' => 'Wade ki tareekh aaj ya baad ki honi chahiye.',
    ];

    public function mount()
    {
        if (! in_array($this->tab, self::TABS, true)) {
            $this->tab = 'overdue';
        }
        if ($this->townId !== '' && ! $this->towns->contains('id', (int) $this->townId)) {
            $this->townId = '';
        }
    }

    #[Computed]
    public function towns()
    {
        return Town::where('user_id', Tenant::ownerId())->orderBy('name')->get(['id', 'name']);
    }

    public function updatedTownId()
    {
        if ($this->townId !== '' && ! $this->towns->contains('id', (int) $this->townId)) {
            $this->townId = '';
        }
    }

    public function setTab($tab)
    {
        $this->tab = in_array($tab, self::TABS, true) ? $tab : 'overdue';
    }

    // WhatsApp button ke saath hi log ho jata hai, taake pata rahe aaj kis ko reminder ja chuka
    public function logReminder($plotId)
    {
        $plot = $this->ownedPlot($plotId);

        $alreadyToday = PlotFollowup::where('plot_id', $plot->id)
            ->where('channel', 'whatsapp')
            ->where('outcome', 'sent')
            ->whereDate('created_at', today())
            ->exists();

        if (! $alreadyToday) {
            PlotFollowup::create([
                'plot_id' => $plot->id,
                'plot_booking_id' => $this->activeBookingId($plot->id),
                'user_id' => Auth::id(),
                'channel' => 'whatsapp',
                'outcome' => 'sent',
            ]);
        }
    }

    public function openFollowup($plotId)
    {
        $plot = $this->ownedPlot($plotId);

        $this->resetValidation();
        $this->fuPlotId = $plot->id;
        $this->fu_channel = 'call';
        $this->fu_outcome = 'reached';
        $this->fu_promise_date = '';
        $this->fu_note = '';
    }

    public function closeFollowup()
    {
        $this->reset(['fuPlotId', 'fu_channel', 'fu_outcome', 'fu_promise_date', 'fu_note']);
        $this->resetValidation();
    }

    public function saveFollowup()
    {
        $plot = $this->ownedPlot($this->fuPlotId);

        $this->validate([
            'fu_channel' => ['required', Rule::in(array_keys(PlotFollowup::CHANNELS))],
            'fu_outcome' => ['required', Rule::in(array_keys(PlotFollowup::OUTCOMES))],
            'fu_promise_date' => 'required_if:fu_outcome,promised|nullable|date|after_or_equal:today',
            'fu_note' => 'nullable|string|max:500',
        ]);

        PlotFollowup::create([
            'plot_id' => $plot->id,
            'plot_booking_id' => $this->activeBookingId($plot->id),
            'user_id' => Auth::id(),
            'channel' => $this->fu_channel,
            'outcome' => $this->fu_outcome,
            'promise_date' => $this->fu_outcome === 'promised' ? $this->fu_promise_date : null,
            'note' => $this->fu_note ? trim($this->fu_note) : null,
        ]);

        session()->flash('success', 'Follow-up darj ho gaya — Plot #' . $plot->plot_number . '.');
        $this->closeFollowup();
    }

    public function render()
    {
        $today = today();
        $todayStr = $today->toDateString();
        $weekEnd = $today->copy()->addDays(self::LOOKAHEAD_DAYS)->toDateString();

        $plotIds = $this->ownedPlotQuery()->select('plots.id');

        // All unpaid installments up to next 7 days (late ones included)
        $unpaid = Installment::with(['plot:id,town_id,plot_number,block_name,size,customer_id,status', 'plot.town:id,name', 'plot.customer:id,name,phone'])
            ->whereIn('plot_id', $plotIds)
            ->whereIn('status', BookingService::UNPAID)
            ->where('due_date', '<=', $weekEnd)
            ->orderBy('due_date')
            ->limit(self::MAX_ROWS)
            ->get();

        $plotIdList = $unpaid->pluck('plot_id')->unique()->values();

        $bookings = PlotBooking::whereIn('plot_id', $plotIdList)->where('status', 'active')->get(['id', 'plot_id', 'portal_token'])->keyBy('plot_id');

        $lastFollowups = PlotFollowup::with('user:id,name')
            ->whereIn('plot_id', $plotIdList)
            ->where('created_at', '>=', now()->subDays(120))
            ->latest('id')
            ->get()
            ->groupBy('plot_id');

        $cases = $unpaid->groupBy('plot_id')->map(fn ($items) => $this->makeCase($items, $todayStr, $bookings, $lastFollowups));

        $stats = [
            'late' => $cases->sum('late_amount'),
            'late_customers' => $cases->where('late_amount', '>', 0)->count(),
            'today' => $cases->sum('today_amount'),
            'today_customers' => $cases->where('today_amount', '>', 0)->count(),
            'week' => $cases->sum('upcoming_amount'),
            'week_customers' => $cases->where('upcoming_amount', '>', 0)->count(),
            'promises' => $cases->filter(fn ($c) => $c->promise && $c->promise->promise_date->lte(today()))->count(),
            'collected_today' => (float) Installment::whereIn('plot_id', $plotIds)
                ->where('status', 'paid')
                ->whereDate('paid_date', $todayStr)
                ->sum('amount'),
        ];

        $list = match ($this->tab) {
            'today' => $cases->filter(fn ($c) => $c->today_amount > 0)->sortByDesc('today_amount'),
            'week' => $cases->filter(fn ($c) => $c->upcoming_amount > 0)->sortBy('next_due'),
            'promises' => $cases->filter(fn ($c) => $c->promise)->sortBy(fn ($c) => $c->promise->promise_date->timestamp),
            default => $cases->filter(fn ($c) => $c->late_amount > 0)->sortBy('oldest_due'),
        };

        $search = mb_strtolower(trim($this->search));
        if ($search !== '') {
            $list = $list->filter(fn ($c) => str_contains(mb_strtolower($c->name . ' ' . $c->phone . ' ' . $c->plot->plot_number . ' ' . $c->plot->block_name . ' ' . $c->town), $search));
        }

        $list = $list->values()->each(function ($c) {
            $c->whatsapp = Messenger::link($c->phone, $this->reminderMessage($c));
        });

        return view('livewire.town-owner.recovery-desk', [
            'towns' => $this->towns,
            'autoSend' => Messenger::isAutomatic(),
            'list' => $list,
            'stats' => $stats,
            'channels' => PlotFollowup::CHANNELS,
            'outcomes' => PlotFollowup::OUTCOMES,
            'followupPlot' => $this->fuPlotId ? $this->ownedPlotQuery()->with('town:id,name')->find($this->fuPlotId) : null,
            'followupHistory' => $this->fuPlotId
                ? PlotFollowup::with('user:id,name')->where('plot_id', $this->fuPlotId)->latest('id')->take(10)->get()
                : collect(),
        ])->layout('layouts.app');
    }

    /** Ek plot (customer) ki tamam baqi qistein => recovery case */
    protected function makeCase($items, string $todayStr, $bookings, $lastFollowups): object
    {
        $plot = $items->first()->plot;
        $late = $items->filter(fn ($i) => (string) $i->due_date < $todayStr);
        $dueToday = $items->filter(fn ($i) => (string) $i->due_date === $todayStr);
        $upcoming = $items->filter(fn ($i) => (string) $i->due_date > $todayStr);
        $history = $lastFollowups->get($plot->id, collect());
        $promise = $history->first(fn ($f) => $f->promise_date !== null);

        return (object) [
            'plot' => $plot,
            'town' => $plot->town?->name,
            'name' => $plot->customer?->name ?? $items->first()->buyer_name,
            'phone' => $plot->customer?->phone ?? $items->first()->buyer_phone,
            'late_amount' => (float) $late->sum('amount'),
            'late_count' => $late->count(),
            'oldest_due' => $late->min('due_date'),
            'today_amount' => (float) $dueToday->sum('amount'),
            'upcoming_amount' => (float) $upcoming->sum('amount'),
            'next_due' => $upcoming->min('due_date'),
            'booking' => $bookings->get($plot->id),
            'last' => $history->first(),
            'promise' => $promise,
            'reminded_today' => $history->contains(fn ($f) => $f->channel === 'whatsapp' && $f->created_at->isToday()),
        ];
    }

    protected function reminderMessage($c): string
    {
        return Messenger::render(...$this->reminderTemplate($c));
    }

    /** Late / aaj due / aane wali: markazi template aur us ke variables (admin portal se alfaaz badle ja sakte hain) */
    protected function reminderTemplate($c): array
    {
        $vars = [
            'name' => $c->name,
            'place' => "{$c->town} — Plot #{$c->plot->plot_number}",
            'portal' => $c->booking?->portal_token ? ' Apna hisaab yahan dekhein: ' . route('portal.show', $c->booking->portal_token) : '',
        ];

        if ($c->late_amount > 0) {
            return ['installment_overdue', $vars + ['count' => $c->late_count, 'amount' => number_format($c->late_amount), 'since' => Carbon::parse($c->oldest_due)->format('d M Y')]];
        }
        if ($c->today_amount > 0) {
            return ['installment_due_today', $vars + ['amount' => number_format($c->today_amount)]];
        }

        return ['installment_due_soon', $vars + ['amount' => number_format($c->upcoming_amount), 'due_date' => Carbon::parse($c->next_due)->format('d M Y')]];
    }

    /** API/SMS laga ho to seedha reminder; kamyab ho to follow-up log mein bhi */
    public function sendReminder($plotId)
    {
        $plot = $this->ownedPlot($plotId);
        $items = Installment::with(['plot:id,town_id,plot_number,block_name,size,customer_id,status', 'plot.town:id,name', 'plot.customer:id,name,phone'])
            ->where('plot_id', $plot->id)
            ->whereIn('status', BookingService::UNPAID)
            ->where('due_date', '<=', today()->addDays(self::LOOKAHEAD_DAYS)->toDateString())
            ->orderBy('due_date')
            ->get();

        if ($items->isEmpty()) {
            session()->flash('error', 'Is customer ki koi baqi qist nahi.');

            return;
        }

        $bookings = PlotBooking::where('plot_id', $plot->id)->where('status', 'active')->get(['id', 'plot_id', 'portal_token'])->keyBy('plot_id');
        $case = $this->makeCase($items, today()->toDateString(), $bookings, collect());
        [$key, $vars] = $this->reminderTemplate($case);

        $r = Messenger::send($case->phone, $key, $vars, ['plot_id' => $plot->id, 'source' => 'recovery']);
        if ($r['ok']) {
            $this->logReminder($plot->id);
            session()->flash('success', 'Reminder bhej diya: ' . ($case->name ?: 'Plot #' . $plot->plot_number));
        } else {
            session()->flash('error', 'Paigham nahi gaya: ' . ($r['error'] ?: 'wajah maloom nahi') . '.');
        }
    }

    protected function ownedPlotQuery()
    {
        return Plot::whereHas('town', fn ($q) => $q->where('user_id', Tenant::ownerId()))
            ->when($this->townId !== '', fn ($q) => $q->where('town_id', $this->townId));
    }

    protected function ownedPlot($id): Plot
    {
        return Plot::whereHas('town', fn ($q) => $q->where('user_id', Tenant::ownerId()))->findOrFail($id);
    }

    protected function activeBookingId($plotId): ?int
    {
        return PlotBooking::where('plot_id', $plotId)->where('status', 'active')->latest('id')->value('id');
    }
}
