<div class="bg-slate-950 min-h-screen text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Top Navigation -->
        <a href="{{ route('housing-schemes') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-amber-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Housing Schemes
        </a>

        <!-- Scheme Header Banner Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-3 gap-8 shadow-2xl">
            <div class="lg:col-span-2 space-y-4">
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-black uppercase tracking-widest rounded-full">
                    NOC: {{ $scheme->noc_number ?? 'Approved / Verified' }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ $scheme->name }}</h1>
                <p class="text-slate-400 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-amber-400"></i> {{ $scheme->location }}, {{ $scheme->city }}
                </p>

                <!-- Key Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4">
                    <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Available Plots</span>
                        <span class="text-xl font-black text-emerald-400">{{ $scheme->available_plots_count ?? 0 }}</span>
                    </div>
                    <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Total Area</span>
                        <span class="text-xl font-black text-slate-200">{{ $scheme->total_area ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">City</span>
                        <span class="text-xl font-black text-slate-200">{{ $scheme->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Map Integration Button Box -->
            @php
                $mapUrl = !empty($scheme->google_map_url) 
                    ? $scheme->google_map_url 
                    : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode(($scheme->location ?? '') . ', ' . ($scheme->city ?? ''));
            @endphp
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex flex-col justify-between items-center text-center space-y-4">
                <i class="fa-solid fa-map-location-dot text-5xl text-amber-400"></i>
                <div>
                    <h4 class="text-white font-bold text-sm">Location Guidance</h4>
                    <p class="text-xs text-slate-400 mt-1">Navigate directly to society location via Google Maps.</p>
                </div>
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition-all shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-route"></i> Open Google Maps
                </a>
            </div>
        </div>

        <!-- Available Plots Inventory Section -->
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-white">Available Plots & Pricing</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Select and review plot sizes, pricing, and category specs.</p>
                </div>

                <div class="flex gap-2">
                    <input type="text" wire:model.live.debounce.300ms="searchPlot" placeholder="Search plot #..." class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-amber-500 w-44">
                </div>
            </div>

            <!-- Plots Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($plots as $plot)
                    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 hover:border-amber-500/40 transition-all shadow-xl space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">Plot No.</span>
                                <h3 class="text-2xl font-extrabold text-white">{{ $plot->plot_number }}</h3>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $plot->status === 'available' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                {{ ucfirst($plot->status ?? 'Available') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs bg-slate-950 p-3 rounded-xl border border-slate-800/60">
                            <div>
                                <span class="text-slate-500 block text-[10px]">Size / Area</span>
                                <strong class="text-slate-200">{{ $plot->size ?? 'N/A' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px]">Category</span>
                                <strong class="text-slate-200">{{ $plot->type ?? 'Residential' }}</strong>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-800/80 flex justify-between items-center">
                            <div>
                                <span class="text-[10px] text-slate-500 block">Total Price</span>
                                <span class="text-lg font-black text-amber-400">PKR {{ number_format($plot->total_price ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-slate-900/40 border border-slate-800 rounded-2xl p-10 text-center text-slate-500">
                        <p class="text-sm">No plots available in this housing scheme right now.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $plots->links() }}
            </div>
        </div>

    </div>
</div>