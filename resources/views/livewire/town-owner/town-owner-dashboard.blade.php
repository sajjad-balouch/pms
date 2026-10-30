<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Header & Navigation Links -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-2xl font-black text-white tracking-tight">Town Owner Dashboard</h2>
            </div>
            <p class="text-sm text-gray-400 mt-1">
                <span lang="ur" dir="rtl">خوش آمدید،</span>
                <span class="font-bold text-indigo-400">{{ auth()->user()->name }}</span>
                <span class="text-gray-600 mx-1">•</span>
                <span>{{ now()->format('l, d M Y') }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('town_owner.towns') }}" wire:navigate class="inline-flex items-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:shadow-indigo-500/10 transition-all duration-200">
                <span>🏘️</span>
                <span>Schemes</span>
            </a>

            <a href="{{ route('town_owner.employees') }}" wire:navigate class="inline-flex items-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:shadow-emerald-500/10 transition-all duration-200">
                <span>👷</span>
                <span>Employees</span>
            </a>

            <a href="{{ route('town_owner.expenses') }}" wire:navigate class="inline-flex items-center gap-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:shadow-rose-500/10 transition-all duration-200">
                <span>💸</span>
                <span>Expenses</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-red-500/20 hover:text-red-400 hover:border-red-500/40 text-gray-400 border border-gray-700 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col lg:flex-row lg:items-end gap-4 bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80">
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <label class="block">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Town / Scheme</span>
                <select wire:model.live="townId" class="mt-1 w-full bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Towns</option>
                    @foreach ($myTowns as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Period</span>
                <select wire:model.live="period" class="mt-1 w-full bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">All Time</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </label>

            @if ($period === 'custom')
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">From</span>
                    <input type="date" wire:model.live="from" class="mt-1 w-full bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 [color-scheme:dark]">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">To</span>
                    <input type="date" wire:model.live="to" class="mt-1 w-full bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 [color-scheme:dark]">
                </label>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <span wire:loading.delay class="text-xs text-indigo-400 animate-pulse">Updating…</span>
            <span wire:loading.remove class="text-[11px] text-gray-500">Updated {{ $cachedAt->diffForHumans() }}</span>
            <button type="button" wire:click="refreshStats" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 disabled:opacity-50">
                🔄 Refresh
            </button>
        </div>
    </div>

    <!-- Today's summary (send on WhatsApp) -->
    <div class="bg-gradient-to-r from-emerald-900/30 to-gray-900/80 p-5 rounded-2xl border border-emerald-500/20 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Aaj ki wasooli</p>
                <p class="text-xl font-black text-emerald-400">Rs. {{ number_format($daily['collected']) }}</p>
                <p class="text-[11px] text-gray-500">{{ $daily['collected_count'] }} entries</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Aaj due (baqi)</p>
                <p class="text-xl font-black text-amber-400">Rs. {{ number_format($daily['due_today']) }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Aaj ke kharche</p>
                <p class="text-xl font-black text-rose-400">Rs. {{ number_format($daily['spent']) }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Nayi bookings</p>
                <p class="text-xl font-black text-white">{{ $daily['booked'] }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($daily['to_self'])
                <a href="{{ $daily['to_self'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-green-600/20 hover:bg-green-600/30 text-green-300 border border-green-500/30 px-3 py-2 rounded-xl text-xs font-bold">💬 Khulasa mujhe bhejein</a>
            @endif
            <a href="{{ $daily['share'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 px-3 py-2 rounded-xl text-xs font-bold">↗ Partner / group ko share</a>
            <a href="{{ route('town_owner.recovery') }}" wire:navigate class="inline-flex items-center gap-1.5 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-3 py-2 rounded-xl text-xs font-bold">📞 Recovery Desk</a>
        </div>
    </div>

    <!-- Overdue Alert Banner -->
    @if ($overdueCount > 0)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-red-500/10 border border-red-500/30 text-red-300 px-5 py-4 rounded-2xl">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="font-bold">{{ number_format($overdueCount) }} installments overdue</p>
                    <p class="text-sm text-red-400/80">Total overdue amount: <span class="font-bold">Rs. {{ number_format($overdueAmount) }}</span></p>
                </div>
            </div>
            <a href="{{ route('town_owner.recovery') }}" wire:navigate class="text-sm font-bold text-red-300 hover:text-red-200 underline underline-offset-4">📞 Recovery Desk kholein →</a>
        </div>
    @endif

    <!-- Quick Inventory Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-purple-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Housing Towns</p>
            <p class="text-3xl font-black text-white mt-2 group-hover:text-purple-400 transition-colors">{{ $towns->count() }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-indigo-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Plots Inventory</p>
            <p class="text-3xl font-black text-white mt-2 group-hover:text-indigo-400 transition-colors">{{ number_format($totalPlots) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-emerald-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Available Plots</p>
            <p class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($availablePlots) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-amber-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Booked / Sold Plots</p>
            <p class="text-3xl font-black text-amber-400 mt-2">{{ number_format($bookedPlots) }}</p>
            <p class="text-xs text-gray-500 mt-1">Booked {{ number_format($bookedOnly) }} • Sold {{ number_format($soldOnly) }}</p>
            <div class="mt-3 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full" style="width: {{ $occupancyRate }}%"></div>
            </div>
            <p class="text-[11px] text-gray-500 mt-1">{{ $occupancyRate }}% inventory sold</p>
        </div>
    </div>

    <!-- Financial Income & Collection Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Cash Collected ({{ $periodLabel }})</p>
            <p class="text-2xl font-black text-emerald-400 mt-2">Rs. {{ number_format($periodCollected) }}</p>
            @if ($period !== 'all')
                <p class="text-xs text-gray-500 mt-1">All time: Rs. {{ number_format($totalCollected) }}</p>
            @endif
        </div>

        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pending Collection (This Month)</p>
            <p class="text-2xl font-black text-blue-400 mt-2">Rs. {{ number_format($dueThisMonth) }}</p>
            <p class="text-xs text-gray-500 mt-1">Already received: Rs. {{ number_format($collectedThisMonth) }}</p>
        </div>

        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-cyan-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">This Month Recovery Rate</p>
            <p class="text-2xl font-black text-cyan-400 mt-2">{{ $monthRecoveryRate }}%</p>
            <div class="mt-3 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $monthRecoveryRate }}%"></div>
            </div>
        </div>

        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-violet-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Receivable (All Pending)</p>
            <p class="text-2xl font-black text-violet-400 mt-2">Rs. {{ number_format($totalReceivable) }}</p>
            <p class="text-xs text-red-400/80 mt-1">Overdue: Rs. {{ number_format($overdueAmount) }}</p>
        </div>
    </div>

    <!-- Expenses, Salary & Net Profit Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Town Expenses ({{ $periodLabel }})</p>
            <p class="text-2xl font-black text-rose-400 mt-2">Rs. {{ number_format($totalExpenses) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Salaries & Advances Paid</p>
            <p class="text-2xl font-black text-orange-400 mt-2">Rs. {{ number_format($totalSalariesPaid) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-600"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Outflow (Expenses + Salary + Refunds)</p>
            <p class="text-2xl font-black text-red-400 mt-2">Rs. {{ number_format($totalOutflow) }}</p>
            @if ($totalRefunds > 0)
                <p class="text-xs text-gray-500 mt-1">Booking refunds: Rs. {{ number_format($totalRefunds) }}</p>
            @endif
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full {{ $netProfit >= 0 ? 'bg-teal-500' : 'bg-red-500' }}"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Net Profit / Balance ({{ $periodLabel }})</p>
            <p class="text-2xl font-black {{ $netProfit >= 0 ? 'text-teal-400' : 'text-red-400' }} mt-2">
                {{ $netProfit < 0 ? '-' : '' }}Rs. {{ number_format(abs($netProfit)) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}
                @if ($transferFees > 0)
                    · incl. transfer fees Rs. {{ number_format($transferFees) }}
                @endif
            </p>
        </div>
    </div>

    <!-- Trend Chart + Expense Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>📈</span> Last 6 Months</h3>
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span> Collection</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-rose-500"></span> Outflow</span>
                </div>
            </div>

            <div class="mt-6 h-56 flex items-stretch gap-2 sm:gap-5">
                @foreach ($trend as $m)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="flex-1 w-full flex items-end justify-center gap-1">
                            <div class="w-1/2 max-w-[24px] bg-emerald-500/80 hover:bg-emerald-400 rounded-t-md transition-colors"
                                 style="height: max(2px, {{ round($m['income'] / $trendMax * 100, 2) }}%)"
                                 title="{{ $m['full'] }} — Collection: Rs. {{ number_format($m['income']) }}"></div>
                            <div class="w-1/2 max-w-[24px] bg-rose-500/80 hover:bg-rose-400 rounded-t-md transition-colors"
                                 style="height: max(2px, {{ round($m['outflow'] / $trendMax * 100, 2) }}%)"
                                 title="{{ $m['full'] }} — Outflow: Rs. {{ number_format($m['outflow']) }}"></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>🧾</span> Expenses by {{ $expenseBreakdownBy }}</h3>
            @php $breakdownTotal = max(1, collect($expenseBreakdown)->sum('total')); @endphp

            <div class="mt-5 space-y-4">
                @forelse ($expenseBreakdown as $row)
                    @php $pct = round($row['total'] / $breakdownTotal * 100); @endphp
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-300 font-semibold truncate pr-2">{{ ucfirst($row['label']) }}</span>
                            <span class="text-gray-400 whitespace-nowrap">Rs. {{ number_format($row['total']) }}</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-[11px] text-gray-500">{{ $pct }}%</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-6 text-center">No expenses in this period.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Overdue Installments Table -->
    <div id="overdue" class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden scroll-mt-6">
        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-800/80">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span>⏰</span> Defaulters / Overdue Installments
                @if ($overdueCount > 0)
                    <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-red-500/15 text-red-400 border border-red-500/30">{{ number_format($overdueCount) }}</span>
                @endif
            </h3>
            @if ($overdueCount > count($overdueRows))
                <p class="text-xs text-gray-500">Showing oldest {{ count($overdueRows) }} of {{ number_format($overdueCount) }}</p>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Customer / Plot</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Town</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Due Date</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Late By</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Amount</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Reminder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($overdueRows as $row)
                        <tr wire:key="overdue-{{ $row['id'] }}" class="hover:bg-gray-800/40 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-white">{{ $row['customer'] ?? 'Plot ' . $row['plot'] }}</span>
                                @if ($row['customer'])
                                    <span class="block text-xs text-gray-500">Plot {{ $row['plot'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $row['town'] }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $row['due_date']->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                    {{ $row['days'] > 60 ? 'bg-red-500/10 text-red-400 border-red-500/30' : ($row['days'] > 30 ? 'bg-orange-500/10 text-orange-400 border-orange-500/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/30') }}">
                                    {{ $row['days'] }} days
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-400 whitespace-nowrap">Rs. {{ number_format($row['amount']) }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if ($row['whatsapp'])
                                    <a href="{{ $row['whatsapp'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-green-600/20 hover:bg-green-600/30 text-green-300 border border-green-500/30 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                        💬 WhatsApp
                                    </a>
                                @else
                                    <span class="text-xs text-gray-600">No phone</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <p class="text-base font-semibold">🎉 No overdue installments</p>
                                <p lang="ur" dir="rtl" class="mt-1">تمام قسطیں وقت پر وصول ہو رہی ہیں۔</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming (next 7 days) + Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-xl overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-gray-800/80">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>📅</span> Due in Next 7 Days</h3>
                <p class="text-sm text-gray-400">{{ number_format($upcomingCount) }} installments • <span class="font-bold text-blue-400">Rs. {{ number_format($upcomingAmount) }}</span></p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                    <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left font-bold">Customer / Plot</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold">Due</th>
                            <th scope="col" class="px-6 py-3 text-right font-bold">Amount</th>
                            <th scope="col" class="px-6 py-3 text-right font-bold">Reminder</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-gray-300">
                        @forelse ($upcomingRows as $row)
                            <tr wire:key="upcoming-{{ $row['id'] }}" class="hover:bg-gray-800/40 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="font-semibold text-white">{{ $row['customer'] ?? 'Plot ' . $row['plot'] }}</span>
                                    <span class="block text-xs text-gray-500">{{ $row['customer'] ? 'Plot ' . $row['plot'] . ' · ' : '' }}{{ $row['town'] }}</span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-gray-400">
                                    {{ $row['due_date']->format('d M') }}
                                    <span class="block text-xs text-blue-400">in {{ $row['days'] }} {{ $row['days'] === 1 ? 'day' : 'days' }}</span>
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-blue-400 whitespace-nowrap">Rs. {{ number_format($row['amount']) }}</td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    @if ($row['whatsapp'])
                                        <a href="{{ $row['whatsapp'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-green-600/20 hover:bg-green-600/30 text-green-300 border border-green-500/30 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                            💬 Remind
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-600">No phone</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No installments due in the next 7 days.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>🕒</span> Recent Activity</h3>

            <ul class="mt-5 space-y-4">
                @forelse ($activity as $item)
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 h-9 w-9 flex items-center justify-center rounded-xl border {{ $item['classes'] }}">{{ $item['icon'] }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-200 truncate">{{ \Illuminate\Support\Str::limit(ucfirst($item['title']), 40) }}</p>
                                @if (! is_null($item['amount']))
                                    <p class="text-sm font-bold text-gray-300 whitespace-nowrap">Rs. {{ number_format($item['amount']) }}</p>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $item['meta'] }}{{ $item['meta'] ? ' · ' : '' }}{{ $item['date']?->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500 py-6 text-center">No activity yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Town Schemes Summary Table -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-800/80">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span>🏠</span> My Housing Projects
            </h3>
            <a href="{{ route('town_owner.towns') }}" wire:navigate class="inline-flex items-center text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
                + Add New Scheme
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Town Name</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">City / Location</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Area</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Plots</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Sales Progress</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">NOC Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($towns as $town)
                        @php
                            $townSold = $town->booked_plots_count + $town->sold_plots_count;
                            $townRate = $town->plots_count > 0 ? round($townSold / $town->plots_count * 100) : 0;
                        @endphp
                        <tr wire:key="town-{{ $town->id }}" class="hover:bg-gray-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">{{ $town->name }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ collect([$town->city, $town->location])->filter()->implode(' - ') ?: '—' }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->total_area ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-indigo-400">{{ number_format($town->plots_count) }}</span>
                                <span class="block text-xs text-gray-500">
                                    <span class="text-emerald-400">{{ $town->available_plots_count }}</span> free •
                                    <span class="text-amber-400">{{ $townSold }}</span> sold
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap min-w-[140px]">
                                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full" style="width: {{ $townRate }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $townRate }}%</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($town->noc_number)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" title="NOC #{{ $town->noc_number }}">
                                        ✓ {{ $town->noc_number }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('town_owner.plots', $town->id) }}" wire:navigate class="inline-flex items-center gap-1.5 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>📍</span> Manage Plots
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <p lang="ur" dir="rtl" class="text-base font-semibold">کوئی ہاؤسنگ سکیم نہیں ملی۔</p>
                                <a href="{{ route('town_owner.towns') }}" wire:navigate lang="ur" dir="rtl" class="text-indigo-400 font-bold hover:underline mt-1 inline-block">
                                    یہاں کلک کر کے نئی سکیم شامل کریں
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
