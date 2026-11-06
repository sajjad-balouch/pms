@php
    $rs = fn ($n) => 'Rs ' . number_format((float) $n);
    // Bari raqam mobile par chhoti: 12.5 lakh, 1.2 crore
    $short = function ($n) {
        $n = (float) $n;
        $a = abs($n);
        $s = $a >= 10000000 ? round($a / 10000000, 2) . ' crore' : ($a >= 100000 ? round($a / 100000, 1) . ' lakh' : number_format($a));

        return ($n < 0 ? '-' : '') . 'Rs ' . $s;
    };
    $actions = array_filter([
        ['recovery', 'town_owner.recovery', [], 'fa-hand-holding-dollar', 'Qist wusooli', 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'],
        ['customers', 'town_owner.customers', [], 'fa-address-book', 'Customers', 'bg-sky-500/15 text-sky-300 border-sky-500/30'],
        ['towns', 'town_owner.towns', [], 'fa-city', 'Schemes', 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30'],
        ['expenses', 'town_owner.expenses', [], 'fa-receipt', 'Kharcha', 'bg-rose-500/15 text-rose-300 border-rose-500/30'],
        ['employees', 'town_owner.employees', [], 'fa-user-tie', 'Employees', 'bg-orange-500/15 text-orange-300 border-orange-500/30'],
        ['leads', 'town_owner.leads', [], 'fa-bullhorn', 'Leads', 'bg-fuchsia-500/15 text-fuchsia-300 border-fuchsia-500/30'],
        ['team', 'town_owner.team', [], 'fa-user-shield', 'Team', 'bg-slate-500/15 text-slate-200 border-slate-500/30'],
    ], fn ($a) => $can[$a[0]] ?? false);
    $toneBg = ['amber' => 'bg-amber-500/10 border-amber-500/30 text-amber-300', 'orange' => 'bg-orange-500/10 border-orange-500/30 text-orange-300',
        'red' => 'bg-red-500/10 border-red-500/30 text-red-300', 'rose' => 'bg-rose-600/15 border-rose-500/40 text-rose-200'];
    $remindLinks = collect($overdueRows)->pluck('whatsapp')->filter()->values()->all();
@endphp

<div class="space-y-5 text-gray-100">

    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-bold text-emerald-200">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-bold text-red-200">{{ session('error') }}</div>
    @endif

    {{-- Salam + filter: ek chhoti patti --}}
    <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4 sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs text-gray-400">{{ now()->format('l, d M Y') }}</p>
                <h1 class="truncate text-xl sm:text-2xl font-black text-white">Salam, {{ auth()->user()->name }}</h1>
            </div>
            <button type="button" wire:click="refreshStats" wire:loading.attr="disabled" title="Refresh"
                    class="shrink-0 h-10 w-10 rounded-xl border border-gray-700 bg-gray-800 text-gray-300 hover:bg-gray-700 disabled:opacity-50">
                <i class="fa-solid fa-rotate" wire:loading.class="fa-spin"></i>
            </button>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2 {{ $period === 'custom' ? 'sm:grid-cols-4' : '' }}">
            <select wire:model.live="townId" aria-label="Town" class="w-full rounded-xl border border-gray-700 bg-gray-950 px-3 py-2.5 text-sm text-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tamam schemes</option>
                @foreach ($myTowns as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="period" aria-label="Period" class="w-full rounded-xl border border-gray-700 bg-gray-950 px-3 py-2.5 text-sm text-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="all">Shuru se ab tak</option>
                <option value="this_month">Is mahine</option>
                <option value="last_month">Pichhle mahine</option>
                <option value="this_year">Is saal</option>
                <option value="custom">Apni tareekhein</option>
            </select>
            @if ($period === 'custom')
                <input type="date" wire:model.live="from" aria-label="From" class="w-full rounded-xl border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-gray-200 [color-scheme:dark]">
                <input type="date" wire:model.live="to" aria-label="To" class="w-full rounded-xl border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-gray-200 [color-scheme:dark]">
            @endif
        </div>
        <p class="mt-2 text-[11px] text-gray-500">
            <span wire:loading.delay class="text-indigo-400">Update ho raha hai…</span>
            <span wire:loading.remove>Update: {{ $cachedAt->diffForHumans() }}</span>
        </p>
    </div>

    {{-- 1. Aaj --}}
    <div class="rounded-2xl border border-emerald-500/25 bg-gradient-to-br from-emerald-900/40 to-gray-900/80 p-4 sm:p-5">
        <h2 class="text-sm font-black uppercase tracking-wider text-emerald-300">Aaj</h2>
        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl bg-gray-950/40 p-3">
                <p class="text-[11px] text-gray-400">Wusooli</p>
                <p class="text-lg sm:text-xl font-black text-emerald-400">{{ $short($daily['collected']) }}</p>
                <p class="text-[11px] text-gray-500">{{ $daily['collected_count'] }} entries</p>
            </div>
            <div class="rounded-xl bg-gray-950/40 p-3">
                <p class="text-[11px] text-gray-400">Aaj due (baqi)</p>
                <p class="text-lg sm:text-xl font-black text-amber-400">{{ $short($daily['due_today']) }}</p>
            </div>
            <div class="rounded-xl bg-gray-950/40 p-3">
                <p class="text-[11px] text-gray-400">Kharche</p>
                <p class="text-lg sm:text-xl font-black text-rose-400">{{ $short($daily['spent']) }}</p>
            </div>
            <div class="rounded-xl bg-gray-950/40 p-3">
                <p class="text-[11px] text-gray-400">Nayi bookings</p>
                <p class="text-lg sm:text-xl font-black text-white">{{ $daily['booked'] }}</p>
            </div>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2">
            @if ($daily['to_self'])
                <a href="{{ $daily['to_self'] }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 rounded-xl border border-green-500/30 bg-green-600/20 px-3 py-2.5 text-xs font-bold text-green-200 hover:bg-green-600/30">
                    <i class="fa-brands fa-whatsapp text-base"></i> Mujhe bhejein
                </a>
            @endif
            <a href="{{ $daily['share'] }}" target="_blank" rel="noopener" class="{{ $daily['to_self'] ? '' : 'col-span-2' }} flex items-center justify-center gap-2 rounded-xl border border-gray-700 bg-gray-800 px-3 py-2.5 text-xs font-bold text-gray-200 hover:bg-gray-700">
                <i class="fa-solid fa-share-nodes"></i> Partner / group
            </a>
        </div>
    </div>

    {{-- 2. Fori kaam --}}
    @if ($actions)
        <div class="grid grid-cols-4 gap-2 sm:grid-cols-7">
            @foreach ($actions as [$section, $route, $params, $icon, $label, $tone])
                <a href="{{ route($route, $params) }}" wire:navigate class="flex flex-col items-center justify-center gap-1.5 rounded-2xl border {{ $tone }} px-1 py-3 text-center hover:brightness-125">
                    <i class="fa-solid {{ $icon }} text-xl"></i>
                    <span class="text-[11px] font-bold leading-tight">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    @endif

    {{-- 3. Chaar aham number --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4">
            <p class="text-[11px] font-bold text-gray-400">Wusooli · {{ $periodLabel }}</p>
            <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-400" title="{{ $rs($periodCollected) }}">{{ $short($periodCollected) }}</p>
            @if ($period !== 'all')
                <p class="text-[11px] text-gray-500">Kul: {{ $short($totalCollected) }}</p>
            @endif
        </div>
        <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4">
            <p class="text-[11px] font-bold text-gray-400">Baqi lena hai</p>
            <p class="mt-1 text-lg sm:text-2xl font-black text-violet-300" title="{{ $rs($totalReceivable) }}">{{ $short($totalReceivable) }}</p>
            <p class="text-[11px] {{ $overdueAmount > 0 ? 'text-red-400' : 'text-gray-500' }}">Late: {{ $short($overdueAmount) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4">
            <p class="text-[11px] font-bold text-gray-400">Is mahine recovery</p>
            <p class="mt-1 text-lg sm:text-2xl font-black text-cyan-300">{{ $monthRecoveryRate }}%</p>
            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-800"><div class="h-full rounded-full bg-cyan-500" style="width: {{ min(100, $monthRecoveryRate) }}%"></div></div>
            <p class="mt-1 text-[11px] text-gray-500">Baqi is mahine: {{ $short($dueThisMonth) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4">
            <p class="text-[11px] font-bold text-gray-400">{{ $netProfit >= 0 ? 'Munafa' : 'Nuqsan' }} · {{ $periodLabel }}</p>
            <p class="mt-1 text-lg sm:text-2xl font-black {{ $netProfit >= 0 ? 'text-teal-300' : 'text-red-400' }}" title="{{ $rs($netProfit) }}">{{ $short($netProfit) }}</p>
            <p class="text-[11px] text-gray-500">Kharch: {{ $short($totalOutflow) }}</p>
        </div>
    </div>

    {{-- 4. Defaulters --}}
    <div id="overdue" class="scroll-mt-20 rounded-2xl border {{ $overdueCount ? 'border-red-500/30' : 'border-gray-800/80' }} bg-gray-900/70 p-4 sm:p-5"
         x-data="{ links: @js($remindLinks), next: 0, open() { if (this.next < this.links.length) { window.open(this.links[this.next], '_blank'); this.next++; } } }">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="flex items-center gap-2 text-base font-black text-white">
                <i class="fa-solid fa-triangle-exclamation text-red-400"></i> Late aqsaat
                <span class="rounded-full border border-red-500/30 bg-red-500/15 px-2 py-0.5 text-xs text-red-300">{{ number_format($overdueCount) }}</span>
            </h2>
            <span class="text-sm font-black text-red-300">{{ $rs($overdueAmount) }}</span>
        </div>

        @if ($overdueCount)
            {{-- Kitni der se late --}}
            <div class="mt-3 grid grid-cols-4 gap-2">
                @foreach ($aging as $b)
                    <div class="rounded-xl border px-2 py-2 text-center {{ $toneBg[$b['tone']] }}">
                        <p class="text-[10px] font-bold opacity-80">{{ $b['label'] }} din</p>
                        <p class="text-sm font-black">{{ $b['count'] }}</p>
                        <p class="truncate text-[10px] opacity-80" title="{{ $rs($b['amount']) }}">{{ $short($b['amount']) }}</p>
                    </div>
                @endforeach
            </div>

            @if ($autoSend && count($remindLinks) > 0)
                {{-- Admin ne API/SMS lagaya hai: ek click mein sab ko --}}
                <button type="button" wire:click="sendAllOverdue" wire:loading.attr="disabled" wire:target="sendAllOverdue"
                        wire:confirm="Dashboard ke {{ count($remindLinks) }} late customers ko reminder bhej dein?"
                        class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-3 py-2.5 text-sm font-black text-white hover:bg-green-500 disabled:opacity-50">
                    <i class="fa-solid fa-paper-plane" wire:loading.class="fa-spin" wire:target="sendAllOverdue"></i>
                    Sab ko reminder bhejein ({{ count($remindLinks) }})
                </button>
            @elseif (count($remindLinks) > 1)
                <button type="button" x-on:click="open()" x-bind:disabled="next >= links.length"
                        class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-3 py-2.5 text-sm font-black text-white hover:bg-green-500 disabled:opacity-50">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    <span x-text="next === 0 ? 'Sab ko baari baari reminder ({{ count($remindLinks) }})' : (next < links.length ? 'Agla reminder (' + (next + 1) + '/' + links.length + ')' : 'Sab ko bhej diya ✓')"></span>
                </button>
            @endif
        @endif

        <div class="mt-3 grid gap-2 md:grid-cols-2">
            @forelse ($overdueRows as $row)
                <div wire:key="overdue-{{ $row['id'] }}" class="flex items-center gap-3 rounded-xl border border-gray-800 bg-gray-950/50 p-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-white">{{ $row['customer'] ?? 'Plot ' . $row['plot'] }}</p>
                        <p class="truncate text-[11px] text-gray-400">Plot {{ $row['plot'] }} · {{ $row['town'] }}</p>
                        <p class="mt-0.5 text-xs">
                            <span class="font-black text-red-300">{{ $rs($row['amount']) }}</span>
                            <span class="ms-1 rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $row['days'] > 60 ? 'bg-red-500/15 text-red-300' : ($row['days'] > 30 ? 'bg-orange-500/15 text-orange-300' : 'bg-amber-500/15 text-amber-300') }}">{{ $row['days'] }} din late</span>
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-1.5">
                        @if ($row['tel'])
                            <a href="{{ $row['tel'] }}" title="Call" class="flex h-10 w-10 items-center justify-center rounded-xl border border-sky-500/30 bg-sky-600/20 text-sky-200 hover:bg-sky-600/30"><i class="fa-solid fa-phone"></i></a>
                        @endif
                        @if ($row['whatsapp'] && $autoSend)
                            <button type="button" wire:click="sendReminder({{ $row['id'] }})" wire:loading.attr="disabled" wire:target="sendReminder({{ $row['id'] }})" title="Reminder bhejein"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-green-500/30 bg-green-600/20 text-green-200 hover:bg-green-600/30 disabled:opacity-50">
                                <i class="fa-solid fa-paper-plane" wire:loading.class="fa-spin" wire:target="sendReminder({{ $row['id'] }})"></i>
                            </button>
                        @elseif ($row['whatsapp'])
                            <a href="{{ $row['whatsapp'] }}" target="_blank" rel="noopener" title="WhatsApp" class="flex h-10 w-10 items-center justify-center rounded-xl border border-green-500/30 bg-green-600/20 text-green-200 hover:bg-green-600/30"><i class="fa-brands fa-whatsapp text-lg"></i></a>
                        @endif
                        @if (! $row['tel'] && ! $row['whatsapp'])
                            <span class="text-[11px] text-gray-500">Number nahi</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-400 md:col-span-2">🎉 Koi qist late nahi. Sab waqt par aa rahi hain.</p>
            @endforelse
        </div>
        @if ($overdueCount > count($overdueRows) && ($can['recovery'] ?? false))
            <a href="{{ route('town_owner.recovery') }}" wire:navigate class="mt-3 block text-center text-sm font-bold text-indigo-300 hover:text-indigo-200">
                Sab {{ number_format($overdueCount) }} dekhein — Recovery Desk →
            </a>
        @endif
    </div>

    {{-- 5. Agle 7 din --}}
    <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4 sm:p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="flex items-center gap-2 text-base font-black text-white"><i class="fa-regular fa-calendar text-sky-400"></i> Agle 7 din</h2>
            <span class="text-xs text-gray-400">{{ $upcomingCount }} aqsaat · <span class="font-bold text-sky-300">{{ $rs($upcomingAmount) }}</span></span>
        </div>
        <div class="mt-3 grid gap-2 md:grid-cols-2">
            @forelse ($upcomingRows as $row)
                <div wire:key="upcoming-{{ $row['id'] }}" class="flex items-center gap-3 rounded-xl border border-gray-800 bg-gray-950/50 p-3">
                    <div class="w-12 shrink-0 rounded-lg bg-sky-500/10 py-1 text-center">
                        <p class="text-base font-black leading-none text-sky-300">{{ $row['due_date']->format('d') }}</p>
                        <p class="text-[10px] text-sky-400">{{ $row['due_date']->format('M') }}</p>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-white">{{ $row['customer'] ?? 'Plot ' . $row['plot'] }}</p>
                        <p class="truncate text-[11px] text-gray-400">Plot {{ $row['plot'] }} · {{ $row['town'] }} · <span class="font-bold text-sky-300">{{ $rs($row['amount']) }}</span></p>
                    </div>
                    @if ($row['whatsapp'] && $autoSend)
                        <button type="button" wire:click="sendReminder({{ $row['id'] }})" wire:loading.attr="disabled" wire:target="sendReminder({{ $row['id'] }})" title="Yaad-dehani bhejein"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-green-500/30 bg-green-600/20 text-green-200 hover:bg-green-600/30 disabled:opacity-50">
                            <i class="fa-solid fa-paper-plane" wire:loading.class="fa-spin" wire:target="sendReminder({{ $row['id'] }})"></i>
                        </button>
                    @elseif ($row['whatsapp'])
                        <a href="{{ $row['whatsapp'] }}" target="_blank" rel="noopener" title="Yaad-dehani" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-green-500/30 bg-green-600/20 text-green-200 hover:bg-green-600/30"><i class="fa-brands fa-whatsapp text-lg"></i></a>
                    @endif
                </div>
            @empty
                <p class="py-4 text-center text-sm text-gray-500 md:col-span-2">Agle 7 din mein koi qist due nahi.</p>
            @endforelse
        </div>
    </div>

    {{-- 6. Schemes --}}
    <div class="rounded-2xl border border-gray-800/80 bg-gray-900/70 p-4 sm:p-5">
        <div class="flex items-center justify-between gap-2">
            <h2 class="flex items-center gap-2 text-base font-black text-white"><i class="fa-solid fa-city text-indigo-400"></i> Schemes aur plots</h2>
            @if ($can['towns'] ?? false)
                <a href="{{ route('town_owner.towns') }}" wire:navigate class="text-xs font-bold text-indigo-300 hover:text-indigo-200">+ Nayi scheme</a>
            @endif
        </div>
        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400">
            <span>Kul plots: <b class="text-white">{{ number_format($totalPlots) }}</b></span>
            <span class="text-emerald-400">Khali: <b>{{ number_format($availablePlots) }}</b></span>
            <span class="text-amber-400">Booked: <b>{{ number_format($bookedOnly) }}</b></span>
            <span class="text-orange-300">Sold: <b>{{ number_format($soldOnly) }}</b></span>
        </div>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            @forelse ($towns as $town)
                @php
                    $sold = $town->booked_plots_count + $town->sold_plots_count;
                    $pct = $town->plots_count > 0 ? round($sold / $town->plots_count * 100) : 0;
                @endphp
                <div wire:key="town-{{ $town->id }}" class="rounded-xl border border-gray-800 bg-gray-950/50 p-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="truncate font-bold text-white">{{ $town->name }}</p>
                            <p class="truncate text-[11px] text-gray-400">{{ collect([$town->city, $town->location])->filter()->implode(' - ') ?: '—' }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $town->noc_number ? 'bg-emerald-500/10 text-emerald-300' : 'bg-amber-500/10 text-amber-300' }}">
                            {{ $town->noc_number ? 'NOC ✓' : 'NOC pending' }}
                        </span>
                    </div>
                    <div class="mt-2 flex h-2 w-full overflow-hidden rounded-full bg-gray-800" title="{{ $pct }}% bik gaye">
                        <div class="h-full bg-orange-400" style="width: {{ $town->plots_count ? $town->sold_plots_count / $town->plots_count * 100 : 0 }}%"></div>
                        <div class="h-full bg-amber-500" style="width: {{ $town->plots_count ? $town->booked_plots_count / $town->plots_count * 100 : 0 }}%"></div>
                    </div>
                    <div class="mt-1.5 flex items-center justify-between text-[11px]">
                        <span class="text-gray-400"><b class="text-emerald-400">{{ $town->available_plots_count }}</b> khali · <b class="text-amber-400">{{ $sold }}</b> bike/booked · {{ $pct }}%</span>
                        @if ($can['plots'] ?? false)
                            <a href="{{ route('town_owner.plots', $town->id) }}" wire:navigate class="font-bold text-indigo-300 hover:text-indigo-200">Plots →</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-6 text-center md:col-span-2">
                    <p class="text-sm text-gray-400">Abhi koi scheme nahi.</p>
                    @if ($can['towns'] ?? false)
                        <a href="{{ route('town_owner.towns') }}" wire:navigate class="mt-1 inline-block text-sm font-bold text-indigo-300">Pehli scheme shamil karein →</a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    {{-- 7. Tafseelat: mobile par band, bari screen par khuli --}}
    <details class="group rounded-2xl border border-gray-800/80 bg-gray-900/70" x-data x-init="if (window.matchMedia('(min-width: 1024px)').matches) $el.open = true">
        <summary class="flex cursor-pointer list-none items-center justify-between p-4 sm:p-5">
            <span class="flex items-center gap-2 text-base font-black text-white"><i class="fa-solid fa-chart-column text-emerald-400"></i> Hisaab ki tafseel</span>
            <i class="fa-solid fa-chevron-down text-gray-400 transition-transform group-open:rotate-180"></i>
        </summary>
        <div class="space-y-5 border-t border-gray-800/80 p-4 sm:p-5">
            {{-- Kharch ki tafseel --}}
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach ([['Kharche', $totalExpenses, 'text-rose-300'], ['Tankhwahen', $totalSalariesPaid, 'text-orange-300'], ['Refunds', $totalRefunds, 'text-red-300'], ['Transfer fees (+)', $transferFees, 'text-teal-300']] as [$label, $value, $color])
                    <div class="rounded-xl bg-gray-950/50 p-3">
                        <p class="text-[11px] text-gray-400">{{ $label }}</p>
                        <p class="font-black {{ $color }}" title="{{ $rs($value) }}">{{ $short($value) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                {{-- 6 mahine --}}
                <div class="lg:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-white">Pichhle 6 mahine</h3>
                        <div class="flex items-center gap-3 text-[11px] text-gray-400">
                            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span> Wusooli</span>
                            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-sm bg-rose-500"></span> Kharch</span>
                        </div>
                    </div>
                    <div class="mt-3 flex h-44 items-stretch gap-2 sm:gap-4">
                        @foreach ($trend as $m)
                            <div class="flex flex-1 flex-col items-center gap-1.5">
                                <div class="flex w-full flex-1 items-end justify-center gap-1">
                                    <div class="w-1/2 max-w-[22px] rounded-t-md bg-emerald-500/80" style="height: max(2px, {{ round($m['income'] / $trendMax * 100, 2) }}%)" title="{{ $m['full'] }} — Wusooli: {{ $rs($m['income']) }}"></div>
                                    <div class="w-1/2 max-w-[22px] rounded-t-md bg-rose-500/80" style="height: max(2px, {{ round($m['outflow'] / $trendMax * 100, 2) }}%)" title="{{ $m['full'] }} — Kharch: {{ $rs($m['outflow']) }}"></div>
                                </div>
                                <span class="text-[11px] text-gray-500">{{ $m['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kharche kahan --}}
                <div>
                    <h3 class="text-sm font-bold text-white">Kharche ({{ $expenseBreakdownBy === 'Category' ? 'qism' : 'scheme' }} ke hisaab se)</h3>
                    @php $breakdownTotal = max(1, collect($expenseBreakdown)->sum('total')); @endphp
                    <div class="mt-3 space-y-3">
                        @forelse ($expenseBreakdown as $row)
                            @php $pct = round($row['total'] / $breakdownTotal * 100); @endphp
                            <div>
                                <div class="flex justify-between gap-2 text-xs">
                                    <span class="truncate font-semibold text-gray-300">{{ ucfirst($row['label']) }}</span>
                                    <span class="whitespace-nowrap text-gray-400">{{ $short($row['total']) }} · {{ $pct }}%</span>
                                </div>
                                <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-gray-800"><div class="h-full rounded-full bg-rose-500" style="width: {{ $pct }}%"></div></div>
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-gray-500">Is muddat mein koi kharcha nahi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Haal hi mein --}}
            <div>
                <h3 class="text-sm font-bold text-white">Haal hi mein</h3>
                <ul class="mt-3 grid gap-2 md:grid-cols-2">
                    @forelse ($activity as $item)
                        <li class="flex items-center gap-3 rounded-xl bg-gray-950/50 p-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border {{ $item['classes'] }}">{{ $item['icon'] }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-200">{{ \Illuminate\Support\Str::limit(ucfirst($item['title']), 40) }}</p>
                                <p class="truncate text-[11px] text-gray-500">{{ $item['meta'] }}{{ $item['meta'] ? ' · ' : '' }}{{ $item['date']?->diffForHumans() }}</p>
                            </div>
                            @if (! is_null($item['amount']))
                                <span class="whitespace-nowrap text-xs font-bold text-gray-300">{{ $short($item['amount']) }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="py-4 text-center text-xs text-gray-500 md:col-span-2">Abhi koi activity nahi.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </details>
</div>
