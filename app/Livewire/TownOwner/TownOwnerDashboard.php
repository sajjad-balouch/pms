<?php

namespace App\Livewire\TownOwner;

use App\Support\Tenant;
use App\Models\EmployeeSalary;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\PlotTransfer;
use App\Models\Town;
use App\Services\Messaging\Messenger;
use App\Support\WhatsApp;
use App\Models\TownExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;

class TownOwnerDashboard extends Component
{
    // Dashboard stats are cached for this many seconds (the Refresh button clears it)
    const CACHE_SECONDS = 120;

    const PERIODS = ['all', 'this_month', 'last_month', 'this_year', 'custom'];

    #[Url(as: 'town', except: '')]
    public $townId = '';

    #[Url(except: 'all')]
    public $period = 'all';

    #[Url(except: '')]
    public $from = '';

    #[Url(except: '')]
    public $to = '';

    protected array $columnListings = [];

    public function updatedPeriod()
    {
        if ($this->period !== 'custom') {
            $this->from = '';
            $this->to = '';
        }
    }

    public function refreshStats()
    {
        Cache::forget($this->cacheKey());
    }

    public function render()
    {
        $myTowns = Town::where('user_id', Tenant::ownerId())->orderBy('name')->get(['id', 'name']);

        // Never trust URL values: only the owner's own towns and known periods
        if ($this->townId !== '' && ! $myTowns->contains('id', (int) $this->townId)) {
            $this->townId = '';
        }
        if (! in_array($this->period, self::PERIODS, true)) {
            $this->period = 'all';
        }

        $stats = Cache::remember($this->cacheKey(), self::CACHE_SECONDS, fn () => $this->buildStats());

        return view('livewire.town-owner.town-owner-dashboard', $stats + [
            'myTowns' => $myTowns,
            'periodLabel' => $this->periodLabel(),
            'daily' => $this->dailySummary($myTowns, $stats),
            // Team member ko sirf woh fori kaam dikhayein jin ki ijazat hai
            'can' => collect(array_keys(Tenant::SECTIONS))->push('team')->mapWithKeys(fn ($s) => [$s => Tenant::can($s)])->all(),
            // Admin ne API/SMS lagaya ho to buttons seedha bhejte hain
            'autoSend' => Messenger::isAutomatic(),
        ])->layout('layouts.app');
    }

    // "Aaj ka khulasa" — one tap to send it on WhatsApp (to self, a partner or the family group)
    protected function dailySummary($myTowns, array $stats): array
    {
        $today = Carbon::today()->toDateString();
        $townIds = $this->townId !== '' ? [(int) $this->townId] : $myTowns->pluck('id')->all();
        $plotIds = Plot::select('id')->whereIn('town_id', $townIds);

        $collected = Installment::whereIn('plot_id', $plotIds)->where('status', 'paid')->whereDate('paid_date', $today)
            ->toBase()->selectRaw('COUNT(*) AS n, COALESCE(SUM(amount + COALESCE(late_fee, 0)), 0) AS total')->first();
        $dueToday = Installment::whereIn('plot_id', $plotIds)->whereIn('status', ['pending', 'overdue'])->whereDate('due_date', $today)
            ->toBase()->selectRaw('COUNT(*) AS n, COALESCE(SUM(amount), 0) AS total')->first();
        $spent = (float) TownExpense::whereIn('town_id', $townIds)->whereDate('expense_date', $today)->sum('amount');
        $bookedToday = Plot::whereIn('town_id', $townIds)->whereDate('booked_at', $today)->count();

        $scope = $this->townId !== '' ? ($myTowns->firstWhere('id', (int) $this->townId)?->name ?? '') : 'Tamam schemes';
        $rs = fn ($n) => 'Rs. ' . number_format((float) $n);

        $message = implode("\n", array_filter([
            '📊 *Aaj ka khulasa* — ' . now()->format('d M Y'),
            '🏘️ ' . $scope,
            '',
            "✅ Wusooli: {$rs($collected->total)} ({$collected->n} entries)",
            "📅 Aaj due (baqi): {$rs($dueToday->total)} ({$dueToday->n})",
            "⚠️ Kul late: {$rs($stats['overdueAmount'])} ({$stats['overdueCount']} qistein)",
            "💸 Aaj ke kharche: {$rs($spent)}",
            $bookedToday ? "📝 Nayi bookings: {$bookedToday}" : null,
            "📈 Is mahine recovery: {$stats['monthRecoveryRate']}%",
        ], fn ($line) => $line !== null));

        return [
            'collected' => (float) $collected->total,
            'collected_count' => (int) $collected->n,
            'due_today' => (float) $dueToday->total,
            'spent' => $spent,
            'booked' => $bookedToday,
            'to_self' => WhatsApp::url(Auth::user()?->phone, $message),
            'share' => WhatsApp::shareUrl($message),
        ];
    }

    protected function buildStats(): array
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();
        $monthStart = $today->copy()->startOfMonth()->toDateString();
        $monthEnd = $today->copy()->endOfMonth()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();
        $range = $this->dateRange();

        // Pick real column names from the DB so the dashboard adapts to the schema
        $paidCol = $this->firstColumn((new Installment)->getTable(), ['paid_at', 'paid_date', 'payment_date'], 'updated_at');
        $expenseTable = (new TownExpense)->getTable();
        $expenseDateCol = $this->firstColumn($expenseTable, ['expense_date', 'date', 'spent_at'], 'created_at');
        $expenseCategoryCol = $this->firstColumn($expenseTable, ['category', 'expense_type', 'type', 'head'], null);
        $salaryDateCol = $this->firstColumn((new EmployeeSalary)->getTable(), ['paid_at', 'paid_date', 'payment_date', 'date'], 'created_at');

        // Towns + per-town plot counts in ONE query (inventory totals are derived from this)
        $towns = Town::where('user_id', Tenant::ownerId())
            ->when($this->townId !== '', fn ($q) => $q->whereKey($this->townId))
            ->withCount([
                'plots',
                'plots as available_plots_count' => fn ($q) => $q->where('status', 'available'),
                'plots as booked_plots_count' => fn ($q) => $q->where('status', 'booked'),
                'plots as sold_plots_count' => fn ($q) => $q->where('status', 'sold'),
            ])
            ->latest()
            ->get();

        $townIds = $towns->pluck('id');

        // Subquery instead of plucking thousands of plot IDs into PHP memory
        $plotIdsQuery = Plot::select('id')->whereIn('town_id', $townIds);

        // Inventory Metrics (no extra queries)
        $totalPlots = $towns->sum('plots_count');
        $availablePlots = $towns->sum('available_plots_count');
        $bookedOnly = $towns->sum('booked_plots_count');
        $soldOnly = $towns->sum('sold_plots_count');
        $bookedPlots = $bookedOnly + $soldOnly;
        $occupancyRate = $totalPlots > 0 ? round($bookedPlots / $totalPlots * 100, 1) : 0;

        // Financial Metrics - Income (all installment figures in ONE aggregate query)
        $bindings = [$monthStart, $monthEnd, $monthStart, $monthEnd, $todayStr, $todayStr, $todayStr, $weekEnd, $todayStr, $weekEnd];
        $periodSql = "COALESCE(SUM(CASE WHEN status = 'paid' THEN amount + COALESCE(late_fee, 0) ELSE 0 END), 0)";
        if ($range) {
            $periodSql = "COALESCE(SUM(CASE WHEN status = 'paid' AND {$paidCol} BETWEEN ? AND ? THEN amount + COALESCE(late_fee, 0) ELSE 0 END), 0)";
            array_push($bindings, ...$range);
        }

        $stats = Installment::whereIn('plot_id', $plotIdsQuery)
            ->toBase()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount + COALESCE(late_fee, 0) ELSE 0 END), 0) AS total_collected,
                COALESCE(SUM(CASE WHEN status IN ('pending', 'overdue') THEN amount ELSE 0 END), 0) AS total_receivable,
                COALESCE(SUM(CASE WHEN status IN ('pending', 'overdue') AND due_date BETWEEN ? AND ? THEN amount ELSE 0 END), 0) AS due_this_month,
                COALESCE(SUM(CASE WHEN status = 'paid' AND due_date BETWEEN ? AND ? THEN amount ELSE 0 END), 0) AS collected_this_month,
                COALESCE(SUM(CASE WHEN status IN ('pending', 'overdue') AND due_date < ? THEN amount ELSE 0 END), 0) AS overdue_amount,
                COALESCE(SUM(CASE WHEN status IN ('pending', 'overdue') AND due_date < ? THEN 1 ELSE 0 END), 0) AS overdue_count,
                COALESCE(SUM(CASE WHEN status IN ('pending', 'overdue') AND due_date > ? AND due_date <= ? THEN amount ELSE 0 END), 0) AS upcoming_amount,
                COALESCE(SUM(CASE WHEN status IN ('pending', 'overdue') AND due_date > ? AND due_date <= ? THEN 1 ELSE 0 END), 0) AS upcoming_count,
                {$periodSql} AS period_collected
            ", $bindings)
            ->first();

        $totalReceivable = (float) $stats->total_receivable;
        $dueThisMonth = (float) $stats->due_this_month;
        $collectedThisMonth = (float) $stats->collected_this_month;
        $periodCollected = (float) $stats->period_collected;

        $monthTarget = $collectedThisMonth + $dueThisMonth;
        $monthRecoveryRate = $monthTarget > 0 ? round($collectedThisMonth / $monthTarget * 100, 1) : 0;

        // Financial Metrics - Expenses & Salaries (respect the selected period)
        $expenseQuery = fn () => TownExpense::whereIn('town_id', $townIds)
            ->when($range, fn ($q) => $q->whereBetween($expenseDateCol, $range));

        $salaryQuery = fn () => EmployeeSalary::whereHas('employee', fn ($q) => $q->whereIn('town_id', $townIds))
            ->when($range, fn ($q) => $q->whereBetween($salaryDateCol, $range));

        $totalExpenses = (float) $expenseQuery()->sum('amount');
        $totalSalariesPaid = (float) $salaryQuery()->sum('amount');

        // Booking cancellations: refunds are outflow; transfer fees are extra income
        $totalRefunds = (float) PlotBooking::whereIn('plot_id', $plotIdsQuery)
            ->where('status', 'cancelled')
            ->where('refund_amount', '>', 0)
            ->when($range, fn ($q) => $q->whereBetween('refund_date', $range))
            ->sum('refund_amount');

        $transferFees = (float) PlotTransfer::whereIn('plot_id', $plotIdsQuery)
            ->when($range, fn ($q) => $q->whereBetween('transfer_date', $range))
            ->sum('transfer_fee');

        $totalOutflow = $totalExpenses + $totalSalariesPaid + $totalRefunds;

        // Net Profit Calculation
        $netProfit = $periodCollected + $transferFees - $totalOutflow;

        // Expense breakdown: by category if the table has one, otherwise by town
        if ($expenseCategoryCol) {
            $expenseBreakdownBy = 'Category';
            $expenseBreakdown = $expenseQuery()->toBase()
                ->selectRaw("{$expenseCategoryCol} AS label, SUM(amount) AS total")
                ->groupBy($expenseCategoryCol)
                ->orderByDesc('total')
                ->limit(6)
                ->get()
                ->map(fn ($r) => ['label' => $r->label ?: 'Other', 'total' => (float) $r->total]);
        } else {
            $expenseBreakdownBy = 'Town';
            $expenseBreakdown = $expenseQuery()->toBase()
                ->selectRaw('town_id, SUM(amount) AS total')
                ->groupBy('town_id')
                ->orderByDesc('total')
                ->limit(6)
                ->get()
                ->map(fn ($r) => [
                    'label' => $towns->firstWhere('id', $r->town_id)?->name ?? 'Town #' . $r->town_id,
                    'total' => (float) $r->total,
                ]);
        }

        // Overdue & upcoming (next 7 days) installment lists
        $relations = ['plot.town'];
        if (method_exists(Plot::class, 'customer')) {
            $relations[] = 'plot.customer';
        }

        // Defaulters kitni der se late hain: 1-30, 31-60, 61-90, 90+ din (raqam aur ginti)
        $d30 = $today->copy()->subDays(30)->toDateString();
        $d60 = $today->copy()->subDays(60)->toDateString();
        $d90 = $today->copy()->subDays(90)->toDateString();
        $agingRow = Installment::whereIn('plot_id', $plotIdsQuery)
            ->whereIn('status', ['pending', 'overdue'])
            ->where('due_date', '<', $todayStr)
            ->toBase()
            ->selectRaw('
                COALESCE(SUM(CASE WHEN due_date >= ? THEN amount ELSE 0 END), 0) AS a1, COALESCE(SUM(CASE WHEN due_date >= ? THEN 1 ELSE 0 END), 0) AS n1,
                COALESCE(SUM(CASE WHEN due_date < ? AND due_date >= ? THEN amount ELSE 0 END), 0) AS a2, COALESCE(SUM(CASE WHEN due_date < ? AND due_date >= ? THEN 1 ELSE 0 END), 0) AS n2,
                COALESCE(SUM(CASE WHEN due_date < ? AND due_date >= ? THEN amount ELSE 0 END), 0) AS a3, COALESCE(SUM(CASE WHEN due_date < ? AND due_date >= ? THEN 1 ELSE 0 END), 0) AS n3,
                COALESCE(SUM(CASE WHEN due_date < ? THEN amount ELSE 0 END), 0) AS a4, COALESCE(SUM(CASE WHEN due_date < ? THEN 1 ELSE 0 END), 0) AS n4
            ', [$d30, $d30, $d30, $d60, $d30, $d60, $d60, $d90, $d60, $d90, $d90, $d90])
            ->first();
        $aging = [
            ['label' => '1–30', 'amount' => (float) $agingRow->a1, 'count' => (int) $agingRow->n1, 'tone' => 'amber'],
            ['label' => '31–60', 'amount' => (float) $agingRow->a2, 'count' => (int) $agingRow->n2, 'tone' => 'orange'],
            ['label' => '61–90', 'amount' => (float) $agingRow->a3, 'count' => (int) $agingRow->n3, 'tone' => 'red'],
            ['label' => '90+', 'amount' => (float) $agingRow->a4, 'count' => (int) $agingRow->n4, 'tone' => 'rose'],
        ];

        $overdueRows = Installment::with($relations)
            ->whereIn('plot_id', $plotIdsQuery)
            ->whereIn('status', ['pending', 'overdue'])
            ->where('due_date', '<', $todayStr)
            ->orderBy('due_date')
            ->limit(20)
            ->get()
            ->map(fn ($i) => $this->installmentRow($i, $today))
            ->all();

        $upcomingRows = Installment::with($relations)
            ->whereIn('plot_id', $plotIdsQuery)
            ->whereIn('status', ['pending', 'overdue'])
            ->where('due_date', '>', $todayStr)
            ->where('due_date', '<=', $weekEnd)
            ->orderBy('due_date')
            ->limit(10)
            ->get()
            ->map(fn ($i) => $this->installmentRow($i, $today))
            ->all();

        // Last 6 months: income vs outflow
        $trendStart = $today->copy()->startOfMonth()->subMonths(5);
        $incomeByMonth = $this->sumByMonth(Installment::whereIn('plot_id', $plotIdsQuery)->where('status', 'paid'), $paidCol, $trendStart, 'amount + COALESCE(late_fee, 0)');
        $expenseByMonth = $this->sumByMonth(TownExpense::whereIn('town_id', $townIds), $expenseDateCol, $trendStart);
        $salaryByMonth = $this->sumByMonth(
            EmployeeSalary::whereHas('employee', fn ($q) => $q->whereIn('town_id', $townIds)),
            $salaryDateCol,
            $trendStart
        );

        $trend = collect(range(5, 0))->map(function ($ago) use ($today, $incomeByMonth, $expenseByMonth, $salaryByMonth) {
            $month = $today->copy()->startOfMonth()->subMonths($ago);
            $key = $month->format('Y-m');

            return [
                'label' => $month->format('M'),
                'full' => $month->format('M Y'),
                'income' => (float) ($incomeByMonth[$key] ?? 0),
                'outflow' => (float) (($expenseByMonth[$key] ?? 0) + ($salaryByMonth[$key] ?? 0)),
            ];
        })->all();

        $trendMax = max(1, collect($trend)->max(fn ($m) => max($m['income'], $m['outflow'])));

        // Recent activity feed: payments, expenses, bookings
        $recentPayments = Installment::with('plot.town')
            ->whereIn('plot_id', $plotIdsQuery)
            ->where('status', 'paid')
            ->whereNotNull($paidCol)
            ->latest($paidCol)
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'icon' => '💰',
                'classes' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                'title' => 'Installment received',
                'meta' => collect([$i->plot?->town?->name, 'Plot ' . ($this->plotLabel($i->plot) ?? '#' . $i->plot_id)])->filter()->implode(' · '),
                'amount' => (float) $i->amount,
                'date' => Carbon::parse($i->{$paidCol}),
            ]);

        $recentExpenses = TownExpense::whereIn('town_id', $townIds)
            ->whereNotNull($expenseDateCol)
            ->latest($expenseDateCol)
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'icon' => '💸',
                'classes' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                'title' => $this->attr($e, array_filter(['title', 'description', 'name', 'details', $expenseCategoryCol])) ?? 'Expense',
                'meta' => $towns->firstWhere('id', $e->town_id)?->name,
                'amount' => (float) $e->amount,
                'date' => Carbon::parse($e->{$expenseDateCol}),
            ]);

        $recentBookings = Plot::whereIn('town_id', $townIds)
            ->whereIn('status', ['booked', 'sold'])
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'icon' => '📍',
                'classes' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'title' => 'Plot ' . ($this->plotLabel($p) ?? '#' . $p->id) . ' ' . $p->status,
                'meta' => $towns->firstWhere('id', $p->town_id)?->name,
                'amount' => null,
                'date' => $p->updated_at,
            ]);

        $activity = $recentPayments->concat($recentExpenses)->concat($recentBookings)
            ->sortByDesc(fn ($a) => $a['date']?->timestamp ?? 0)
            ->take(8)
            ->values()
            ->all();

        return [
            'towns' => $towns,
            'totalPlots' => $totalPlots,
            'availablePlots' => $availablePlots,
            'bookedOnly' => $bookedOnly,
            'soldOnly' => $soldOnly,
            'bookedPlots' => $bookedPlots,
            'occupancyRate' => $occupancyRate,
            'totalCollected' => (float) $stats->total_collected,
            'periodCollected' => $periodCollected,
            'totalReceivable' => $totalReceivable,
            'dueThisMonth' => $dueThisMonth,
            'collectedThisMonth' => $collectedThisMonth,
            'monthRecoveryRate' => $monthRecoveryRate,
            'overdueAmount' => (float) $stats->overdue_amount,
            'overdueCount' => (int) $stats->overdue_count,
            'upcomingAmount' => (float) $stats->upcoming_amount,
            'upcomingCount' => (int) $stats->upcoming_count,
            'overdueRows' => $overdueRows,
            'aging' => $aging,
            'upcomingRows' => $upcomingRows,
            'totalExpenses' => $totalExpenses,
            'totalSalariesPaid' => $totalSalariesPaid,
            'totalRefunds' => $totalRefunds,
            'transferFees' => $transferFees,
            'totalOutflow' => $totalOutflow,
            'netProfit' => $netProfit,
            'expenseBreakdown' => $expenseBreakdown->values()->all(),
            'expenseBreakdownBy' => $expenseBreakdownBy,
            'trend' => $trend,
            'trendMax' => $trendMax,
            'activity' => $activity,
            'cachedAt' => now(),
        ];
    }

    protected function installmentRow(Installment $installment, Carbon $today): array
    {
        $plot = $installment->plot;
        $customer = $plot && $plot->relationLoaded('customer') ? $plot->customer : null;
        $due = Carbon::parse($installment->due_date);

        $name = $this->attr($customer, ['name', 'full_name'])
            ?? $this->attr($installment, ['buyer_name'])
            ?? $this->attr($plot, ['customer_name', 'owner_name', 'buyer_name']);
        $phone = $this->attr($customer, ['phone', 'mobile', 'phone_number', 'contact'])
            ?? $this->attr($installment, ['buyer_phone'])
            ?? $this->attr($plot, ['customer_phone', 'owner_phone', 'buyer_phone']);

        $plotLabel = $this->plotLabel($plot) ?? '#' . $installment->plot_id;
        $town = $plot?->town?->name ?? '—';
        [$key, $vars] = $this->reminderFor($installment, $name, $town, $plotLabel, $due, $today);

        return [
            'id' => $installment->id,
            'town' => $town,
            'plot' => $plotLabel,
            'customer' => $name,
            'phone' => $phone,
            // Paigham markazi template se (admin portal se badla ja sakta hai)
            'whatsapp' => Messenger::link($phone, Messenger::render($key, $vars)),
            // Mobile par seedha call
            'tel' => strlen(preg_replace('/\D+/', '', (string) $phone)) >= 10 ? 'tel:' . preg_replace('/[^\d+]/', '', (string) $phone) : null,
            'due_date' => $due,
            'days' => (int) abs($today->diffInDays($due)),
            'amount' => (float) $installment->amount,
        ];
    }

    /** Late qist => "installment_overdue", aane wali => "installment_due_soon" */
    protected function reminderFor(Installment $i, ?string $name, string $town, string $plotLabel, Carbon $due, Carbon $today): array
    {
        $vars = ['name' => $name, 'place' => "{$town} — Plot #{$plotLabel}", 'amount' => number_format((float) $i->amount), 'portal' => ''];

        return $due->lt($today)
            ? ['installment_overdue', $vars + ['count' => 1, 'since' => $due->format('d M Y')]]
            : ['installment_due_soon', $vars + ['due_date' => $due->format('d M Y')]];
    }

    /**
     * API/SMS laga ho to dashboard se seedha reminder. Sirf apne owner ki qist; nakami par link wapas.
     */
    public function sendReminder(int $installmentId): void
    {
        $result = $this->sendFor($installmentId);
        $this->flashResult($result['ok'] ? 1 : 0, $result['ok'] ? 0 : 1, $result['error'] ?? null);
    }

    /** Dashboard ke tamam late customers ko ek click mein (rozana had ke andar) */
    public function sendAllOverdue(): void
    {
        $ids = Installment::whereIn('plot_id', $this->ownedPlotIds())->whereIn('status', ['pending', 'overdue'])
            ->where('due_date', '<', Carbon::today()->toDateString())->orderBy('due_date')->limit(20)->pluck('id');

        [$sent, $failed, $error] = [0, 0, null];
        foreach ($ids as $id) {
            $r = $this->sendFor($id);
            $r['ok'] ? $sent++ : $failed++;
            $error ??= $r['ok'] ? null : $r['error'];
            if (in_array($r['status'] ?? '', ['limited', 'no_balance'], true)) {
                break;
            }
        }
        $this->flashResult($sent, $failed, $error);
    }

    protected function sendFor(int $installmentId): array
    {
        $relations = method_exists(Plot::class, 'customer') ? ['plot.town', 'plot.customer'] : ['plot.town'];
        $i = Installment::with($relations)->whereIn('plot_id', $this->ownedPlotIds())->whereKey($installmentId)->first();
        if (! $i) {
            return ['ok' => false, 'status' => 'failed', 'error' => 'Qist nahi mili'];
        }

        $row = $this->installmentRow($i, Carbon::today());
        [$key, $vars] = $this->reminderFor($i, $row['customer'], $row['town'], $row['plot'], Carbon::parse($i->due_date), Carbon::today());

        return Messenger::send($row['phone'], $key, $vars, ['installment_id' => $i->id, 'source' => 'dashboard']);
    }

    protected function ownedPlotIds()
    {
        return Plot::select('id')->whereIn('town_id', Town::where('user_id', Tenant::ownerId())->select('id'));
    }

    protected function flashResult(int $sent, int $failed, ?string $error): void
    {
        if ($sent) {
            session()->flash('success', "{$sent} paigham bhej diye gaye." . ($failed ? " {$failed} nahi gaye." : ''));
        }
        if (! $sent && $failed) {
            session()->flash('error', 'Paigham nahi gaya: ' . ($error ?: 'wajah maloom nahi') . ' — WhatsApp link se bhej dein.');
        }
    }

    protected function sumByMonth($query, string $column, Carbon $start, string $sumExpr = 'amount'): array
    {
        $base = $query->toBase();

        $expr = match ($base->getConnection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            'sqlsrv' => "FORMAT({$column}, 'yyyy-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };

        return $base
            ->where($column, '>=', $start->toDateTimeString())
            ->selectRaw("{$expr} AS ym, SUM({$sumExpr}) AS total")
            ->groupByRaw($expr)
            ->pluck('total', 'ym')
            ->all();
    }

    protected function dateRange(): ?array
    {
        $today = Carbon::today();

        [$start, $end] = match ($this->period) {
            'this_month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => [$this->parseDate($this->from), $this->parseDate($this->to) ?? $today->copy()],
            default => [null, null],
        };

        if (! $start || ! $end) {
            return null;
        }
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        return [$start->startOfDay()->toDateTimeString(), $end->endOfDay()->toDateTimeString()];
    }

    protected function parseDate($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function periodLabel(): string
    {
        $range = $this->dateRange();

        return match ($this->period) {
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'custom' => $range
                ? Carbon::parse($range[0])->format('d M Y') . ' – ' . Carbon::parse($range[1])->format('d M Y')
                : 'All Time',
            default => 'All Time',
        };
    }

    protected function cacheKey(): string
    {
        return 'town-owner-dashboard:' . Tenant::ownerId() . ':' . md5(implode('|', [
            $this->townId, $this->period, $this->from, $this->to, Carbon::today()->toDateString(),
        ]));
    }

    protected function firstColumn(string $table, array $candidates, ?string $fallback): ?string
    {
        $this->columnListings[$table] ??= Schema::getColumnListing($table);

        foreach ($candidates as $column) {
            if (in_array($column, $this->columnListings[$table], true)) {
                return $column;
            }
        }

        return $fallback;
    }

    // Read a raw attribute safely (works even with Model::preventAccessingMissingAttributes)
    protected function attr($model, array $keys)
    {
        if (! $model) {
            return null;
        }

        $attributes = $model->getAttributes();
        foreach ($keys as $key) {
            if (! empty($attributes[$key])) {
                return $attributes[$key];
            }
        }

        return null;
    }

    protected function plotLabel($plot): ?string
    {
        return $this->attr($plot, ['plot_number', 'plot_no', 'number', 'name']);
    }
}
