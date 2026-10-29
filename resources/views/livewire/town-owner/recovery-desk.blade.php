<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">
    @php
        $tabs = [
            'overdue' => ['⚠️ Late', $stats['late_customers'], 'Rs. ' . number_format($stats['late'])],
            'today' => ['📅 Aaj Due', $stats['today_customers'], 'Rs. ' . number_format($stats['today'])],
            'week' => ['🗓️ Agle 7 Din', $stats['week_customers'], 'Rs. ' . number_format($stats['week'])],
            'promises' => ['🤝 Wade', $stats['promises'], 'aaj tak due'],
        ];
    @endphp

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2"><span>📞</span> Recovery Desk</h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">Aaj kis se wasooli karni hai — reminder bhejein, call ka natija aur adayegi ka wada darj karein.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if ($towns->count() > 1)
                <select wire:model.live="townId" class="bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="">All Towns</option>
                    @foreach ($towns as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            @endif
            <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl px-4 py-2">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">Aaj ki wasooli</p>
                <p class="text-lg font-black text-emerald-400">Rs. {{ number_format($stats['collected_today']) }}</p>
            </div>
            <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold">← Dashboard</a>
        </div>
    </div>

    @if (session('success'))
        <div wire:key="flash-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium">✅ {{ session('success') }}</div>
    @endif

    <!-- Tabs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($tabs as $key => $tabInfo)
            @php [$labelText, $count, $sub] = $tabInfo; @endphp
            <button type="button" wire:click="setTab('{{ $key }}')"
                class="text-left p-5 rounded-2xl border transition-all {{ $tab === $key ? 'bg-indigo-600/15 border-indigo-500/60 ring-1 ring-indigo-500/40' : 'bg-gray-900/60 border-gray-800/80 hover:border-gray-700' }}">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $labelText }}</p>
                <p class="text-2xl font-black text-white mt-1">{{ $count }} <span class="text-xs font-semibold text-gray-500">customers</span></p>
                <p class="text-xs {{ $key === 'overdue' && $stats['late'] > 0 ? 'text-red-400' : 'text-gray-500' }} mt-0.5">{{ $sub }}</p>
            </button>
        @endforeach
    </div>

    <!-- List -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-800/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h3 class="font-extrabold text-white">{{ $tabs[$tab][0] }} <span class="text-sm font-semibold text-gray-500">({{ $list->count() }})</span></h3>
            <div class="relative w-full sm:w-72">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Naam, phone, plot #..."
                    class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">🔍</span>
            </div>
        </div>

        <div class="divide-y divide-gray-800/60" wire:loading.class="opacity-60" wire:target="setTab,townId,search">
            @forelse ($list as $c)
                @php
                    $lateDays = $c->oldest_due ? (int) \Carbon\Carbon::parse($c->oldest_due)->diffInDays(today()) : 0;
                    $promiseBroken = $c->promise && $c->promise->promise_date->lt(today());
                @endphp
                <div wire:key="case-{{ $c->plot->id }}" class="p-5 flex flex-col lg:flex-row lg:items-center gap-4 hover:bg-gray-800/30 transition-colors">
                    <!-- Who -->
                    <div class="lg:w-1/4 min-w-0">
                        <p class="font-bold text-white truncate">{{ $c->name ?: 'Unknown buyer' }}</p>
                        <p class="text-xs text-gray-400">
                            Plot #{{ $c->plot->plot_number }} · {{ $c->plot->block_name ?? 'Main' }}
                            @if ($towns->count() > 1) · {{ $c->town }} @endif
                        </p>
                        @if ($c->phone)
                            <a href="tel:{{ $c->phone }}" class="text-xs text-sky-400 hover:text-sky-300">{{ $c->phone }}</a>
                        @else
                            <span class="text-xs text-gray-600">No phone</span>
                        @endif
                    </div>

                    <!-- Amounts -->
                    <div class="lg:w-1/4 flex flex-wrap gap-x-5 gap-y-1">
                        @if ($c->late_amount > 0)
                            <div>
                                <p class="text-[10px] font-bold uppercase text-gray-500">Late</p>
                                <p class="font-black text-red-400">Rs. {{ number_format($c->late_amount) }}</p>
                                <p class="text-[11px] {{ $lateDays > 60 ? 'text-red-400' : ($lateDays > 30 ? 'text-orange-400' : 'text-amber-400') }}">{{ $c->late_count }} qist · {{ $lateDays }} din</p>
                            </div>
                        @endif
                        @if ($c->today_amount > 0)
                            <div>
                                <p class="text-[10px] font-bold uppercase text-gray-500">Aaj</p>
                                <p class="font-black text-amber-400">Rs. {{ number_format($c->today_amount) }}</p>
                            </div>
                        @endif
                        @if ($c->upcoming_amount > 0)
                            <div>
                                <p class="text-[10px] font-bold uppercase text-gray-500">Agli</p>
                                <p class="font-black text-blue-400">Rs. {{ number_format($c->upcoming_amount) }}</p>
                                <p class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($c->next_due)->format('d M') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Last follow-up -->
                    <div class="lg:flex-1 min-w-0 text-xs">
                        @if ($c->promise)
                            <p class="font-bold {{ $promiseBroken ? 'text-red-400' : ($c->promise->promise_date->isToday() ? 'text-amber-400' : 'text-emerald-400') }}">
                                🤝 Wada: {{ $c->promise->promise_date->format('d M Y') }}
                                {{ $promiseBroken ? '(toot gaya)' : ($c->promise->promise_date->isToday() ? '(aaj)' : '') }}
                            </p>
                        @endif
                        @if ($c->last)
                            <p class="text-gray-400 truncate">
                                {{ $channels[$c->last->channel] ?? $c->last->channel }} · {{ $outcomes[$c->last->outcome] ?? $c->last->outcome }}
                                · {{ $c->last->created_at->diffForHumans() }}
                                @if ($c->last->user) · {{ $c->last->user->name }} @endif
                            </p>
                            @if ($c->last->note)
                                <p class="text-gray-500 truncate" title="{{ $c->last->note }}">"{{ $c->last->note }}"</p>
                            @endif
                        @else
                            <p class="text-gray-600">Abhi koi follow-up nahi</p>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                        @if ($c->whatsapp)
                            <a href="{{ $c->whatsapp }}" target="_blank" rel="noopener" wire:click="logReminder({{ $c->plot->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold border {{ $c->reminded_today ? 'bg-gray-800 text-gray-400 border-gray-700' : 'bg-green-600/20 hover:bg-green-600/30 text-green-300 border-green-500/30' }}">
                                💬 {{ $c->reminded_today ? 'Bhej diya ✓' : 'Reminder' }}
                            </a>
                        @endif
                        <button type="button" wire:click="openFollowup({{ $c->plot->id }})" class="inline-flex items-center gap-1 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">📝 Follow-up</button>
                        <a href="{{ route('town_owner.installments', $c->plot->id) }}" wire:navigate class="inline-flex items-center gap-1 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">💰 Receive</a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <p class="text-base font-semibold">🎉 Is list mein koi customer nahi</p>
                    <p class="text-sm mt-1">{{ $tab === 'overdue' ? 'Koi qist late nahi.' : 'Sab theek hai.' }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Follow-up Modal -->
    @if ($followupPlot)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto" x-data x-on:keydown.escape.window="$wire.closeFollowup()">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeFollowup"></div>
            <div class="relative w-full max-w-lg bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl my-8">
                <div class="p-6 border-b border-gray-800 flex justify-between items-start gap-4">
                    <div>
                        <h3 class="text-lg font-black text-white">📝 Follow-up</h3>
                        <p class="text-sm text-gray-400">{{ $followupPlot->town?->name }} · Plot #{{ $followupPlot->plot_number }}</p>
                    </div>
                    <button type="button" wire:click="closeFollowup" class="h-8 w-8 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300">✕</button>
                </div>

                <form wire:submit="saveFollowup" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Rabta</label>
                            <select wire:model="fu_channel" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm">
                                @foreach ($channels as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Natija</label>
                            <select wire:model.live="fu_outcome" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm">
                                @foreach ($outcomes as $k => $v)
                                    @if ($k !== 'sent')
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('fu_outcome') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if ($fu_outcome === 'promised')
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Kab tak adayegi ka wada? *</label>
                            <input type="date" min="{{ now()->toDateString() }}" wire:model="fu_promise_date" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm [color-scheme:dark]">
                            @error('fu_promise_date') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Note</label>
                        <textarea wire:model="fu_note" rows="2" maxlength="500" placeholder="e.g. Tankhwah 10 tareekh ko milegi" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm placeholder-gray-600"></textarea>
                        @error('fu_note') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="closeFollowup" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveFollowup" class="bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">Save</button>
                    </div>
                </form>

                @if ($followupHistory->isNotEmpty())
                    <div class="px-6 pb-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Pichhle follow-ups</p>
                        <ul class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach ($followupHistory as $f)
                                <li class="text-xs bg-gray-950/60 border border-gray-800 rounded-lg px-3 py-2">
                                    <span class="text-gray-300 font-semibold">{{ $channels[$f->channel] ?? $f->channel }} · {{ $outcomes[$f->outcome] ?? $f->outcome }}</span>
                                    @if ($f->promise_date) <span class="text-emerald-400">· wada {{ $f->promise_date->format('d M') }}</span> @endif
                                    <span class="text-gray-500">· {{ $f->created_at->format('d M, h:i A') }}{{ $f->user ? ' · ' . $f->user->name : '' }}</span>
                                    @if ($f->note) <p class="text-gray-500 mt-0.5">{{ $f->note }}</p> @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
