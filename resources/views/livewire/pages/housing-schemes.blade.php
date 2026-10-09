<div class="bg-slate-950 min-h-screen text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-10">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800 pb-8">
            <div>
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                    <i class="fa-solid fa-city mr-1"></i> {{__("Approved Projects")}}
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white mt-3 tracking-tight">{{__("Housing Schemes")}}</h1>
                <p class="text-slate-400 text-sm mt-2">{{__("Explore government-verified LDA, CDA, and RDA approved housing societies.")}}</p>
            </div>
            
            <!-- Dynamic Search -->
            <div class="flex items-center gap-3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search society or location..." class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500 w-64">
            </div>
        </div>

        <!-- Dynamic Schemes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($schemes as $scheme)
                @php
                    $mapUrl = !empty(trim($scheme->google_map_url ?? '')) 
                        ? $scheme->google_map_url 
                        : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(($scheme->location ?? '') . ', ' . ($scheme->city ?? ''));
                @endphp

                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden hover:border-amber-500/40 transition-all duration-300 shadow-xl group flex flex-col justify-between">
                    <div>
                        <div class="relative h-48 bg-slate-800 overflow-hidden">
                            <img src="{{ $scheme->cover_image ? asset('storage/' . $scheme->cover_image) : 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=600' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-amber-400 text-[10px] font-bold px-3 py-1 rounded-full border border-amber-500/20">
                                {{ $scheme->noc_number ?? '__("Approved")' }}
                            </span>

                            <!-- Map Button (DB Link or Generated Address Link) -->
                            <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" title="View Location Map" class="absolute top-3 right-3 bg-emerald-500/90 hover:bg-emerald-600 text-white text-[11px] font-bold px-3 py-1 rounded-full backdrop-blur-md transition-all flex items-center gap-1 shadow-lg">
                                <i class="fa-solid fa-location-dot"></i> {{__("Map")}}
                            </a>
                        </div>
                        <div class="p-6 space-y-4">
                            <h3 class="text-xl font-bold text-white group-hover:text-amber-400 transition-colors">{{ $scheme->name }}</h3>
                            
                            <!-- Location with Map Link -->
                            <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-amber-400"></i> 
                                <a href="{{ $mapUrl }}" target="_blank" class="hover:underline hover:text-amber-400 transition-colors">
                                    {{ $scheme->location }}
                                </a>
                            </p>

                            <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                                <div><span class="text-slate-500">{{__("Available Plots:")}}</span> <strong class="text-emerald-400 font-bold ml-1">{{ $scheme->available_plots_count ?? 0 }}</strong></div>
                                <div><span class="text-slate-500">{{__("City:")}}</span> <strong class="text-slate-200 ml-1">{{ $scheme->city ?? 'N/A' }}</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-0 space-y-2">
                        <a href="{{ route('housing-schemes.show', $scheme->id) }}" class="block text-center py-2.5 w-full bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-white font-bold text-xs rounded-xl transition-all">
                            {{__("View Complete Details")}}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-slate-900/40 border border-slate-800/80 rounded-3xl p-12 text-center text-slate-500">
                    <i class="fa-solid fa-building-circle-exclamation text-4xl mb-3 text-amber-500/40"></i>
                    <p class="text-sm">No housing schemes found.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $schemes->links() }}
        </div>

    </div>
</div>