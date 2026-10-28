<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">
    @php
        $input = 'w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 disabled:opacity-50';
        $label = 'block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1';
        $statusBadge = [
            'available' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'booked' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'sold' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
        ];
    @endphp

    <!-- Booking / cancel / transfer / details modals -->
    <livewire:town-owner.plot-booking-manager :town-id="$townId" />

    <!-- Top Header & Back Link -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">📍</span>
                <h2 class="text-2xl font-black text-white tracking-tight">Plot Inventory: {{ $town->name }}</h2>
            </div>
            <p class="text-xs text-gray-400 mt-1 font-medium">{{ collect([$town->city, $town->location])->filter()->implode(' — ') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">🏠 Dashboard</a>
            <a href="{{ route('town_owner.towns') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm"><span>←</span> Back to Towns</a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div wire:key="flash-success-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3"><span>✅</span><span>{{ session('success') }}</span></div>
            <button type="button" @click="show = false" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div wire:key="flash-error-{{ md5(session('error')) }}" x-data="{ show: true }" x-show="show" x-transition
             class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3"><span>⛔</span><span>{{ session('error') }}</span></div>
            <button type="button" @click="show = false" class="text-rose-400/60 hover:text-rose-400">&times;</button>
        </div>
    @endif

    <!-- Inventory Summary (click to filter) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['key' => '', 'label' => 'Total Plots', 'count' => $stats->total, 'note' => 'Worth Rs. ' . number_format($stats->inventory_value), 'bar' => 'bg-indigo-500', 'text' => 'text-white'],
            ['key' => 'available', 'label' => 'Available', 'count' => $stats->available, 'note' => 'Worth Rs. ' . number_format($stats->available_value), 'bar' => 'bg-emerald-500', 'text' => 'text-emerald-400'],
            ['key' => 'booked', 'label' => 'Booked', 'count' => $stats->booked, 'note' => 'Booked + Sold Rs. ' . number_format($stats->sold_value), 'bar' => 'bg-amber-500', 'text' => 'text-amber-400'],
            ['key' => 'sold', 'label' => 'Sold', 'count' => $stats->sold, 'note' => null, 'bar' => 'bg-rose-500', 'text' => 'text-rose-400'],
        ] as $card)
            <button type="button" wire:click="$set('statusFilter', '{{ $card['key'] }}')"
                class="text-left bg-gray-900/60 p-5 rounded-2xl border shadow-lg relative overflow-hidden transition-all duration-200 {{ $statusFilter === $card['key'] ? 'border-indigo-500/60 ring-1 ring-indigo-500/40' : 'border-gray-800/80 hover:border-gray-700' }}">
                <div class="absolute top-0 left-0 w-1 h-full {{ $card['bar'] }}"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $card['label'] }}</p>
                <p class="text-3xl font-black mt-1 {{ $card['text'] }}">{{ number_format($card['count']) }}</p>
                @if ($card['note'])
                    <p class="text-[11px] text-gray-500 mt-1">{{ $card['note'] }}</p>
                @endif
            </button>
        @endforeach
    </div>

    <!-- Add / Edit Plot Form Card -->
    <div x-on:plot-form-opened.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
         class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border {{ $editingPlotId ? 'border-amber-500/40' : 'border-gray-800/80' }} shadow-2xl space-y-6 scroll-mt-6">
        <div class="border-b border-gray-800/80 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    @if ($editingPlotId)
                        <span>✏️</span> Edit Plot #{{ $plot_number }}
                    @else
                        <span>➕</span> {{ $mode === 'bulk' ? 'Bulk Add Plots' : 'Add Single Plot to Inventory' }}
                    @endif
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $mode === 'bulk' && ! $editingPlotId ? 'Ek hi block ke bohat se plots (jaise 1 se 50) ek sath add karein.' : 'Plot ki details, features aur payment plan yahan darj karein.' }}
                </p>
            </div>

            @if ($editingPlotId)
                <button type="button" wire:click="cancelEdit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl transition-colors">✕ Cancel Edit</button>
            @else
                <div class="inline-flex bg-gray-950 border border-gray-800 rounded-xl p-1 text-xs font-bold">
                    <button type="button" wire:click="setMode('single')" class="px-3 py-1.5 rounded-lg transition-colors {{ $mode === 'single' ? 'bg-indigo-600/30 text-indigo-300' : 'text-gray-400 hover:text-white' }}">Single</button>
                    <button type="button" wire:click="setMode('bulk')" class="px-3 py-1.5 rounded-lg transition-colors {{ $mode === 'bulk' ? 'bg-indigo-600/30 text-indigo-300' : 'text-gray-400 hover:text-white' }}">Bulk (Range)</button>
                </div>
            @endif
        </div>

        @if ($editingLocked)
            <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 px-4 py-2.5 rounded-xl text-xs">
                🔒 Ye plot booked/sold hai — qeemat, features aur payment plan booking ke waqt fix ho chuke hain. Sirf number, block, type aur size badal sakte hain.
            </div>
        @endif

        <form wire:submit="savePlot" class="space-y-5">
            <!-- Row 1: identity -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                @if ($mode === 'bulk' && ! $editingPlotId)
                    <div>
                        <label class="{{ $label }}">From Plot No. *</label>
                        <input type="number" min="1" wire:model="bulk_from" placeholder="1" class="{{ $input }}" />
                        @error('bulk_from') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">To Plot No. *</label>
                        <input type="number" min="1" wire:model="bulk_to" placeholder="50" class="{{ $input }}" />
                        @error('bulk_to') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div>
                        <label class="{{ $label }}">Plot No. *</label>
                        <input type="text" wire:model="plot_number" placeholder="e.g. 102" class="{{ $input }}" required />
                        @error('plot_number') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div>
                    <label class="{{ $label }}">Block / Sector</label>
                    <input type="text" wire:model="block_name" list="block-options" placeholder="e.g. Block A" class="{{ $input }}" />
                    <datalist id="block-options">
                        @foreach ($blocks as $b)
                            <option value="{{ $b }}"></option>
                        @endforeach
                    </datalist>
                    @error('block_name') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Type *</label>
                    <select wire:model="type" class="{{ $input }}">
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                    </select>
                    @error('type') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Size *</label>
                    <select wire:model.live="size" class="{{ $input }}">
                        @foreach ($sizes as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('size') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: pricing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                <div>
                    <label class="{{ $label }}">Rate per Marla (Rs)</label>
                    <input type="number" step="1" min="0" wire:model.live.debounce.400ms="rate_per_marla" placeholder="e.g. 500000" @disabled($editingLocked) class="{{ $input }}" />
                    <span class="text-[11px] text-gray-500 mt-1 block">Optional — likhne par base price khud ban jayegi.</span>
                    @error('rate_per_marla') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Base Price (Rs) *</label>
                    <input type="number" step="1" min="0" wire:model="base_price" placeholder="2500000" @disabled($editingLocked) class="{{ $input }}" required />
                    @error('base_price') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Down Payment (Rs) *</label>
                    <input type="number" step="1" min="0" wire:model="down_payment" placeholder="500000" @disabled($editingLocked) class="{{ $input }}" required />
                    @error('down_payment') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Installments (Months) *</label>
                    <input type="number" min="1" max="360" wire:model="total_installments" placeholder="36" @disabled($editingLocked) class="{{ $input }}" required />
                    @error('total_installments') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 3: features + live total (calculated in the browser) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5"
                 x-data="{ premiums: @js(collect($featureOptions)->map(fn ($f) => $f['premium'])) }">
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Plot Features (extra charges)</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($featureOptions as $key => $f)
                            <label class="inline-flex items-center gap-2 bg-gray-950 border border-gray-800 hover:border-sky-500/40 rounded-xl px-3 py-2 text-sm text-gray-300 cursor-pointer has-[:checked]:border-sky-500/60 has-[:checked]:bg-sky-500/10 has-[:checked]:text-sky-300 {{ $editingLocked ? 'opacity-50 pointer-events-none' : '' }}">
                                <input type="checkbox" wire:model="features" value="{{ $key }}" class="rounded bg-gray-900 border-gray-700 text-sky-500 focus:ring-sky-500">
                                {{ $f['label'] }} <span class="text-xs text-gray-500">+{{ $f['premium'] }}%</span>
                            </label>
                        @endforeach
                    </div>
                    @error('features.*') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="bg-gray-950/60 border border-gray-800 rounded-xl px-4 py-3 text-xs space-y-1"
                     x-data="{
                        get premium() { return ($wire.features || []).reduce((s, f) => s + (premiums[f] || 0), 0) },
                        get total() { return Math.round((Number($wire.base_price) || 0) * (1 + this.premium / 100)) },
                        get balance() { return Math.max(0, this.total - (Number($wire.down_payment) || 0)) },
                        rs(n) { return 'Rs. ' + Math.round(n).toLocaleString('en-PK') }
                     }">
                    <p class="text-gray-400 font-bold uppercase tracking-wider">Price Summary</p>
                    <p class="text-gray-300">Total: <span class="font-black text-white text-sm" x-text="rs(total)"></span>
                        <span class="text-sky-400" x-show="premium > 0" x-text="'(+' + premium + '%)'"></span></p>
                    <p class="text-gray-300">Balance: <span class="font-bold text-amber-400" x-text="rs(balance)"></span></p>
                    <p class="text-gray-300">Monthly: <span class="font-bold text-indigo-400"
                        x-text="Number($wire.total_installments) > 0 ? rs(balance / Number($wire.total_installments)) : '—'"></span></p>
                    @if ($mode === 'bulk' && ! $editingPlotId)
                        <p class="text-amber-400" x-show="Number($wire.bulk_to) >= Number($wire.bulk_from) && Number($wire.bulk_from) > 0"
                           x-text="(Number($wire.bulk_to) - Number($wire.bulk_from) + 1) + ' plots add honge'"></p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="savePlot"
                    class="inline-flex items-center justify-center gap-2 {{ $editingPlotId ? 'bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border-amber-500/30' : 'bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border-indigo-500/30' }} border font-bold py-2.5 px-6 rounded-xl text-sm shadow-sm transition-all duration-200 disabled:opacity-50">
                    <span wire:loading.remove wire:target="savePlot">{{ $editingPlotId ? 'Update Plot' : ($mode === 'bulk' ? '+ Add Plots in Range' : '+ Add Plot') }}</span>
                    <span wire:loading wire:target="savePlot" class="animate-pulse">Saving...</span>
                </button>
                @if ($editingPlotId)
                    <button type="button" wire:click="cancelEdit" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Cancel</button>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Price Update -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl">
        <button type="button" wire:click="$toggle('showBulkPrice')" class="w-full flex items-center justify-between p-5 text-left">
            <span class="font-bold text-white flex items-center gap-2">💹 Bulk Price Update <span class="text-xs font-normal text-gray-500">(sirf available plots)</span></span>
            <span class="text-gray-400 text-sm">{{ $showBulkPrice ? '▲' : '▼' }}</span>
        </button>

        @if ($showBulkPrice)
            <form wire:submit="applyBulkPrice" class="px-5 pb-5 space-y-4 border-t border-gray-800/80 pt-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="{{ $label }}">Block</label>
                        <select wire:model.live="bp_block" class="{{ $input }}">
                            <option value="">All Blocks</option>
                            <option value="__none">Main (no block)</option>
                            @foreach ($blocks as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">Type</label>
                        <select wire:model.live="bp_type" class="{{ $input }}">
                            <option value="">All Types</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">Size</label>
                        <select wire:model.live="bp_size" class="{{ $input }}">
                            <option value="">All Sizes</option>
                            @foreach ($sizes as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">Method</label>
                        <select wire:model.live="bp_method" class="{{ $input }}">
                            <option value="percent">Increase / decrease by %</option>
                            <option value="amount">Add / subtract fixed Rs</option>
                            <option value="rate">Set new rate per marla</option>
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">
                            {{ $bp_method === 'percent' ? 'Percent (e.g. 10 or -5)' : ($bp_method === 'rate' ? 'Rate per Marla (Rs)' : 'Amount (Rs, e.g. 200000)') }}
                        </label>
                        <input type="number" step="any" wire:model="bp_value" class="{{ $input }}">
                        @error('bp_value') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-gray-400"><span class="font-bold text-white">{{ number_format($bulkPriceCount) }}</span> available plots update honge. Features ka premium khud dobara lagega.</p>
                    <button type="submit" wire:confirm="{{ $bulkPriceCount }} plots ki qeemat badal di jayegi. Continue?" @disabled($bulkPriceCount === 0)
                        class="bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-40">
                        Apply Price Update
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Plots Inventory -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800/80 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h3 class="text-lg font-extrabold text-white">
                    Plots {{ $viewMode === 'grid' ? 'Map' : 'List' }}
                    @if ($plots)
                        <span class="text-sm font-semibold text-gray-500">({{ $plots->total() }})</span>
                    @endif
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="inline-flex bg-gray-950 border border-gray-800 rounded-xl p-1 text-xs font-bold">
                        <button type="button" wire:click="$set('viewMode', 'list')" class="px-3 py-1.5 rounded-lg {{ $viewMode === 'list' ? 'bg-indigo-600/30 text-indigo-300' : 'text-gray-400 hover:text-white' }}">☰ List</button>
                        <button type="button" wire:click="$set('viewMode', 'grid')" class="px-3 py-1.5 rounded-lg {{ $viewMode === 'grid' ? 'bg-indigo-600/30 text-indigo-300' : 'text-gray-400 hover:text-white' }}">▦ Map</button>
                    </div>
                    <button type="button" wire:click="exportCsv" wire:loading.attr="disabled" wire:target="exportCsv"
                        class="inline-flex items-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-4 py-2 rounded-xl text-xs font-bold disabled:opacity-50">⬇️ Export CSV</button>
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="relative lg:col-span-2">
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Plot #, block, customer, CNIC, phone..."
                        class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">🔍</span>
                </div>
                <select wire:model.live="statusFilter" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                    <option value="sold">Sold</option>
                </select>
                <select wire:model.live="blockFilter" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="">All Blocks</option>
                    <option value="__none">Main (no block)</option>
                    @foreach ($blocks as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <select wire:model.live="typeFilter" class="flex-1 bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">All Types</option>
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                    </select>
                    @if ($hasFilters)
                        <button type="button" wire:click="clearFilters" title="Clear filters" class="px-3 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 rounded-xl text-sm">✕</button>
                    @endif
                </div>
            </div>
        </div>

        <div wire:loading.class="opacity-60" wire:target="search,statusFilter,blockFilter,typeFilter,clearFilters,viewMode,gotoPage,nextPage,previousPage">
            @if ($viewMode === 'grid')
                {{-- ================= GRID / MAP VIEW ================= --}}
                <div class="p-6 space-y-6">
                    <div class="flex flex-wrap gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-emerald-500/70"></span> Available (click = Book)</span>
                        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-amber-500/70"></span> Booked</span>
                        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-rose-500/70"></span> Sold</span>
                        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded ring-2 ring-sky-400 bg-gray-800"></span> Has features</span>
                    </div>

                    @forelse ($gridBlocks as $blockName => $blockPlots)
                        <div wire:key="grid-block-{{ md5($blockName) }}">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-white">{{ $blockName }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $blockPlots->count() }} plots ·
                                    <span class="text-emerald-400">{{ $blockPlots->where('status', 'available')->count() }} free</span> ·
                                    <span class="text-amber-400">{{ $blockPlots->where('status', 'booked')->count() }} booked</span> ·
                                    <span class="text-rose-400">{{ $blockPlots->where('status', 'sold')->count() }} sold</span>
                                </p>
                            </div>
                            <div class="grid grid-cols-[repeat(auto-fill,minmax(3.5rem,1fr))] gap-1.5">
                                @foreach ($blockPlots as $p)
                                    @php
                                        $cell = match ($p->status) {
                                            'available' => 'bg-emerald-500/20 hover:bg-emerald-500/40 border-emerald-500/40 text-emerald-200',
                                            'booked' => 'bg-amber-500/20 hover:bg-amber-500/40 border-amber-500/40 text-amber-200',
                                            default => 'bg-rose-500/20 hover:bg-rose-500/40 border-rose-500/40 text-rose-200',
                                        };
                                        $tip = '#' . $p->plot_number . ' · ' . $p->size . ' · Rs. ' . number_format($p->total_price)
                                            . ($p->customer ? ' · ' . $p->customer->name : '')
                                            . (($fl = \App\Support\PlotPricing::featureLabels($p->features)) ? ' · ' . implode(', ', $fl) : '');
                                    @endphp
                                    <button type="button" wire:key="cell-{{ $p->id }}" title="{{ $tip }}"
                                        x-on:click="$dispatch('open-plot-action', { plotId: {{ $p->id }}, action: '{{ $p->status === 'available' ? 'book' : 'details' }}' })"
                                        class="h-12 rounded-lg border text-xs font-bold flex flex-col items-center justify-center transition-colors {{ $cell }} {{ ! empty($p->features) ? 'ring-2 ring-sky-400/70' : '' }}">
                                        <span>{{ $p->plot_number }}</span>
                                        <span class="text-[9px] font-medium opacity-70">{{ str_replace([' Marla', ' Kanal'], ['M', 'K'], $p->size) }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-10">{{ $hasFilters ? 'In filters ke mutabiq koi plot nahi mila.' : 'No plots added to this town yet.' }}</p>
                    @endforelse
                </div>
            @else
                {{-- ================= LIST VIEW ================= --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                        <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Plot #</th>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Type & Size</th>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Price</th>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Payment Plan</th>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Status / Customer</th>
                                <th scope="col" class="px-5 py-4 text-right font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60 text-gray-300">
                            @forelse ($plots as $plot)
                                @php
                                    $monthly = $plot->total_installments > 0 ? max(0, $plot->total_price - $plot->down_payment) / $plot->total_installments : 0;
                                    $featureLabels = \App\Support\PlotPricing::featureLabels($plot->features);
                                @endphp
                                <tr wire:key="plot-{{ $plot->id }}" class="hover:bg-gray-800/40 transition-colors {{ $editingPlotId == $plot->id ? 'bg-amber-500/10 border-l-4 border-l-amber-500' : '' }}">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="font-black text-white">#{{ $plot->plot_number }}</span>
                                        <span class="block text-xs text-gray-500">{{ $plot->block_name ?? 'Main' }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="uppercase text-xs font-bold {{ $plot->type === 'commercial' ? 'text-sky-400' : 'text-indigo-400' }}">{{ $plot->type }}</span>
                                        <span class="text-gray-400 text-xs">({{ $plot->size }})</span>
                                        @if ($featureLabels)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach ($featureLabels as $f)
                                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-sky-500/10 text-sky-300 border border-sky-500/20">{{ $f }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="font-bold text-white">Rs. {{ number_format($plot->total_price) }}</span>
                                        @if ($plot->rate_per_marla)
                                            <span class="block text-[11px] text-gray-500">@ Rs. {{ number_format($plot->rate_per_marla) }}/marla</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-emerald-400">Rs. {{ number_format($plot->down_payment) }}</span>
                                        <span class="text-xs text-gray-500">down</span>
                                        <span class="block text-xs text-gray-400">{{ $plot->total_installments }} × Rs. {{ number_format($monthly) }}</span>
                                        @if ($plot->installments_count > 0)
                                            @php $paidPct = round($plot->paid_installments_count / $plot->installments_count * 100); @endphp
                                            <div class="mt-1 flex items-center gap-2">
                                                <div class="h-1 w-20 bg-gray-800 rounded-full overflow-hidden"><div class="h-full bg-emerald-500" style="width: {{ $paidPct }}%"></div></div>
                                                <span class="text-[11px] text-gray-500">{{ $plot->paid_installments_count }}/{{ $plot->installments_count }} paid</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusBadge[$plot->status] ?? $statusBadge['sold'] }}">{{ ucfirst($plot->status) }}</span>
                                        @if ($plot->customer)
                                            <span class="block text-xs text-gray-300 mt-1 font-semibold">{{ $plot->customer->name }}</span>
                                            <span class="block text-[11px] text-gray-500">{{ $plot->customer->phone }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap space-x-1">
                                        @if ($plot->status === 'available')
                                            <button type="button" x-on:click="$dispatch('open-plot-action', { plotId: {{ $plot->id }}, action: 'book' })"
                                                class="inline-flex items-center gap-1 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">📝 Book</button>
                                        @elseif ($plot->status === 'booked')
                                            <button type="button" wire:click="updateStatus({{ $plot->id }}, 'sold')" wire:confirm="Plot #{{ $plot->plot_number }} ko Sold mark karein?"
                                                class="inline-flex items-center gap-1 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">Mark Sold</button>
                                        @else
                                            <button type="button" wire:click="updateStatus({{ $plot->id }}, 'booked')" wire:confirm="Plot #{{ $plot->plot_number }} ko wapas Booked karein?"
                                                class="inline-flex items-center gap-1 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-3 py-1.5 rounded-xl text-xs font-bold">↩ Undo Sold</button>
                                        @endif

                                        @if ($plot->status !== 'available')
                                            <a href="{{ route('town_owner.installments', $plot->id) }}" wire:navigate title="Installments"
                                                class="inline-flex items-center bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-2.5 py-1.5 rounded-xl text-xs font-bold">💳</a>
                                        @endif

                                        @if ($plot->status !== 'available' || $plot->bookings_count > 0)
                                            <button type="button" title="Details, transfer, cancel & history" x-on:click="$dispatch('open-plot-action', { plotId: {{ $plot->id }}, action: 'details' })"
                                                class="inline-flex items-center bg-sky-600/10 hover:bg-sky-600/20 text-sky-300 border border-sky-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">📋</button>
                                        @endif

                                        <button type="button" wire:click="editPlot({{ $plot->id }})" title="Edit"
                                            class="inline-flex items-center bg-amber-600/10 hover:bg-amber-600/20 text-amber-300 border border-amber-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">✏️</button>

                                        @if ($plot->status === 'available' && $plot->bookings_count == 0 && $plot->installments_count == 0)
                                            <button type="button" wire:click="deletePlot({{ $plot->id }})" wire:confirm="Plot #{{ $plot->plot_number }} hamesha ke liye delete karein?" title="Delete"
                                                class="inline-flex items-center bg-rose-600/10 hover:bg-rose-600/20 text-rose-300 border border-rose-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">🗑️</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        @if ($hasFilters)
                                            <p class="text-base font-semibold">In filters ke mutabiq koi plot nahi mila.</p>
                                            <button type="button" wire:click="clearFilters" class="text-indigo-400 font-bold hover:underline mt-1">Clear filters</button>
                                        @else
                                            <p class="text-base font-semibold">No plots added to this town yet.</p>
                                            <p class="text-xs mt-1">Upar "Bulk (Range)" se ek sath bohat se plots add kar sakte hain.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($plots->hasPages())
                    <div class="p-4 border-t border-gray-800/80 bg-gray-950/30">
                        {{ $plots->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
