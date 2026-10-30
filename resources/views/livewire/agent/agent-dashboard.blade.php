<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    @php
        $statusLabels = ['new' => __('New'), 'contacted' => __('Contacted'), 'site_visit' => __('Site Visit'), 'negotiation' => __('Negotiation'), 'closed_won' => __('Closed Won'), 'closed_lost' => __('Closed Lost')];
        $statusBars = ['new' => 'bg-blue-500', 'contacted' => 'bg-purple-500', 'site_visit' => 'bg-amber-500', 'negotiation' => 'bg-emerald-500'];
        $funnelMax = max(1, collect($funnel)->max('count'));
    @endphp

    <!-- Hero / Header Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-900/90 via-slate-900 to-indigo-950 rounded-2xl p-6 sm:p-8 text-white shadow-lg border border-indigo-500/20">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('Real Estate Portal') }}
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">{{ __('Agent Portal & Leads Dashboard') }}</h2>
                <p class="text-slate-300 text-sm font-normal">{{ __('Welcome') }} <span class="font-semibold text-white">{{ auth()->user()->name }}</span> · {{ now()->translatedFormat('l, d M Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($summaryToSelf)
                    <a href="{{ $summaryToSelf }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-green-600/20 hover:bg-green-600/30 text-green-300 border border-green-500/30 font-semibold px-4 py-2.5 rounded-xl text-sm">💬 {{ __("Today's Summary") }}</a>
                @endif
                <a href="{{ route('agent.properties') }}" wire:navigate class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold px-4 py-2.5 rounded-xl text-sm">🏠 {{ __('Listings') }}</a>
                <a href="{{ route('agent.leads') }}" wire:navigate class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-lg shadow-indigo-950/50">📋 {{ __('Manage All Leads') }}</a>
            </div>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="relative bg-slate-800/90 border border-slate-700/80 p-5 rounded-2xl overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Active Leads') }}</p>
            <p class="text-3xl font-black text-white mt-1">{{ $stats['active'] }}</p>
            <p class="text-[11px] text-slate-500 mt-1">🔥 {{ __(':count hot', ['count' => $stats['hot']]) }} · {{ __('+:count this week', ['count' => $stats['new_week']]) }}</p>
        </div>
        <div class="relative bg-slate-800/90 border border-slate-700/80 p-5 rounded-2xl overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Pipeline Value') }}</p>
            <p class="text-2xl font-black text-amber-400 mt-1">Rs. {{ number_format($stats['pipeline_value']) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">{{ __('Site visit + negotiation (budget)') }}</p>
        </div>
        <div class="relative bg-slate-800/90 border border-slate-700/80 p-5 rounded-2xl overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Closed Deals') }}</p>
            <p class="text-3xl font-black text-emerald-400 mt-1">{{ $stats['won'] }}</p>
            <p class="text-[11px] text-slate-500 mt-1">{{ __('Conversion: :percent%', ['percent' => $stats['conversion']]) }}</p>
        </div>
        <a href="{{ route('agent.commission.ledger') }}" wire:navigate class="relative bg-slate-800/90 border border-slate-700/80 hover:border-slate-600 p-5 rounded-2xl overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-teal-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Commission') }}</p>
            <p class="text-2xl font-black text-teal-400 mt-1">Rs. {{ number_format($commission->received) }}</p>
            <p class="text-[11px] {{ $commission->pending > 0 ? 'text-rose-400' : 'text-slate-500' }} mt-1">
                {{ __('Pending: Rs. :amount', ['amount' => number_format($commission->pending)]) }} · {{ __('This month: Rs. :amount', ['amount' => number_format($commissionMonth)]) }}
            </p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Follow-ups -->
        <div class="lg:col-span-2 bg-slate-800/90 rounded-2xl border border-slate-700/80 overflow-hidden">
            <div class="flex flex-wrap justify-between items-center gap-2 px-6 py-4 border-b border-slate-700/80">
                <h3 class="text-base font-bold text-white">📞 {{ __("Today's Follow-ups") }}</h3>
                <div class="flex gap-2 text-[11px] font-bold">
                    <span class="px-2 py-0.5 rounded-full {{ $lateCount ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-slate-700 text-slate-400' }}">{{ __(':count late', ['count' => $lateCount]) }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ __(':count today', ['count' => $todayCount]) }}</span>
                    @if ($noFollowup)
                        <span class="px-2 py-0.5 rounded-full bg-slate-700 text-slate-300" title="{{ __('Open leads without a next follow-up date') }}">{{ __(':count without date', ['count' => $noFollowup]) }}</span>
                    @endif
                </div>
            </div>
            <div class="divide-y divide-slate-700/60">
                @forelse ($followups as $l)
                    @php $late = $l->next_followup_at->lt(now()); @endphp
                    <div wire:key="fu-{{ $l->id }}" class="px-6 py-3 flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-white truncate">
                                {{ $l->client_name }}
                                @if ($l->priority === 'hot') <span class="text-xs">🔥</span> @endif
                            </p>
                            <p class="text-xs text-slate-400 truncate">
                                {{ $statusLabels[$l->status] ?? $l->status }} · {{ $l->town?->name ?? __('General') }}{{ $l->plot ? ' #' . $l->plot->plot_number : '' }}
                                · <span class="{{ $late ? 'text-rose-400 font-bold' : 'text-amber-400' }}">{{ $late ? $l->next_followup_at->diffForHumans() : $l->next_followup_at->format('h:i A') }}</span>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <a href="tel:{{ $l->client_phone }}" title="{{ __('Call') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-sky-600/20 text-sky-300 border border-sky-500/30">📞</a>
                            @if ($l->whatsapp)
                                <a href="{{ $l->whatsapp }}" target="_blank" rel="noopener" title="{{ __('WhatsApp') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-green-600/20 text-green-300 border border-green-500/30">💬</a>
                            @endif
                            <button type="button" wire:click="snooze({{ $l->id }}, 1)" title="{{ __('Done — follow up again tomorrow') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-700 hover:bg-slate-600 text-slate-200">✓ {{ __('Tomorrow') }}</button>
                            <button type="button" wire:click="snooze({{ $l->id }}, 3)" title="{{ __('In 3 days') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-700 hover:bg-slate-600 text-slate-200">+3d</button>
                            <button type="button" wire:click="snooze({{ $l->id }}, 7)" title="{{ __('In 1 week') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-700 hover:bg-slate-600 text-slate-200">+7d</button>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-slate-500 text-sm">🎉 {{ __('No follow-ups pending today.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Pipeline funnel -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 p-6">
            <h3 class="text-base font-bold text-white mb-4">🧭 {{ __('Pipeline') }}</h3>
            <div class="space-y-3">
                @foreach ($funnel as $f)
                    <div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-300 font-semibold">{{ $statusLabels[$f['status']] }}</span>
                            <span class="text-slate-400">{{ $f['count'] }}{{ $f['value'] > 0 ? ' · Rs. ' . number_format($f['value']) : '' }}</span>
                        </div>
                        <div class="mt-1 h-2 bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full {{ $statusBars[$f['status']] }}" style="width: {{ round($f['count'] / $funnelMax * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-[11px] text-slate-500 mt-4">{{ __('Deals won: :won · Conversion :percent%', ['won' => $stats['won'], 'percent' => $stats['conversion']]) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Portal leads -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700/80 flex justify-between items-center gap-3">
                <h3 class="text-base font-bold text-white">🔓 {{ __('New Portal Leads') }} <span class="text-xs font-normal text-slate-400">({{ __('unlocked your contact') }})</span></h3>
                <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ __(':count this week', ['count' => $stats['portal_week']]) }}</span>
            </div>
            <div class="divide-y divide-slate-700/60">
                @forelse ($portalLeads as $l)
                    <div wire:key="pl-{{ $l->id }}" class="px-6 py-3 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-white truncate">{{ $l->client_name }} <span class="text-xs">🔥</span></p>
                            <p class="text-xs text-slate-400 truncate">{{ $l->property?->title ?? $l->town?->name ?? __('Profile') }} · {{ $l->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="tel:{{ $l->client_phone }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-sky-600/20 text-sky-300 border border-sky-500/30">📞 {{ __('Call') }}</a>
                        @if ($l->whatsapp)
                            <a href="{{ $l->whatsapp }}" target="_blank" rel="noopener" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-green-600/20 text-green-300 border border-green-500/30">💬</a>
                        @endif
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-slate-500 text-sm">{{ __('When a buyer unlocks your listing contact, they will show up here instantly.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Listings performance -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-white">🏠 {{ __('Listings Performance') }}</h3>
                <a href="{{ route('agent.properties') }}" wire:navigate class="text-xs font-bold text-indigo-400 hover:text-indigo-300">{{ __('Manage') }} →</a>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-slate-900/60 rounded-xl p-3"><p class="text-[11px] text-slate-500 uppercase font-bold">{{ __('Active') }}</p><p class="text-xl font-black text-white">{{ $listingStats['active'] }}</p></div>
                <div class="bg-slate-900/60 rounded-xl p-3"><p class="text-[11px] text-slate-500 uppercase font-bold">{{ __('Views') }}</p><p class="text-xl font-black text-indigo-400">{{ number_format($listingStats['views']) }}</p></div>
                <div class="bg-slate-900/60 rounded-xl p-3"><p class="text-[11px] text-slate-500 uppercase font-bold">{{ __('Unlocks (month)') }}</p><p class="text-xl font-black text-emerald-400">{{ $listingStats['unlocks_month'] }}</p></div>
            </div>
            @if ($listingStats['stale'] > 0)
                <a href="{{ route('agent.properties') }}" wire:navigate class="block mt-4 text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-xl px-3 py-2">
                    ⏳ {{ __(':count listings not updated for 30+ days — refresh them to move them up.', ['count' => $listingStats['stale']]) }}
                </a>
            @endif
            @if ($topListings->isNotEmpty())
                <p class="text-[11px] font-bold uppercase text-slate-500 mt-4 mb-2">{{ __('Most viewed') }}</p>
                <ul class="space-y-1.5 text-sm">
                    @foreach ($topListings as $p)
                        <li class="flex justify-between gap-3">
                            <a href="{{ route('property.details', $p->id) }}" target="_blank" class="text-slate-200 hover:text-white truncate">{{ $p->title }}</a>
                            <span class="text-slate-400 whitespace-nowrap">👁 {{ number_format($p->views_count) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Recent Leads Table Card -->
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="flex justify-between items-center px-6 py-5 border-b border-slate-700/80">
            <h3 class="text-base font-bold text-white flex items-center gap-2"><span class="text-rose-400">📋</span> {{ __('Recent Client Leads') }}</h3>
            <a href="{{ route('agent.leads') }}" wire:navigate class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors">{{ __('View All') }} &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-6 py-4 text-start">{{ __('Client Name') }}</th>
                        <th class="px-6 py-4 text-start">{{ __('Phone') }}</th>
                        <th class="px-6 py-4 text-start">{{ __('Interest') }}</th>
                        <th class="px-6 py-4 text-start">{{ __('Budget') }}</th>
                        <th class="px-6 py-4 text-start">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-end">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse ($recentLeads as $lead)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-white">{{ $lead->client_name }} @if ($lead->source === 'portal') <span class="text-[10px] text-emerald-400">{{ __('PORTAL') }}</span> @endif</td>
                            <td class="px-6 py-4 text-slate-300" dir="ltr">{{ $lead->client_phone }}</td>
                            <td class="px-6 py-4 text-slate-300 font-medium">{{ $lead->town?->name ?? __('General Inquiry') }}</td>
                            <td class="px-6 py-4 font-bold text-emerald-400">{{ $lead->budget ? 'Rs. ' . number_format($lead->budget) : __('N/A') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold
                                    {{ $lead->status === 'closed_won' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $lead->status === 'new' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                    {{ $lead->status === 'negotiation' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ in_array($lead->status, ['contacted', 'site_visit']) ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                                    {{ $lead->status === 'closed_lost' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}">
                                    {{ $statusLabels[$lead->status] ?? $lead->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-end text-slate-400 text-xs">{{ $lead->created_at->translatedFormat('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">{{ __('No leads found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
