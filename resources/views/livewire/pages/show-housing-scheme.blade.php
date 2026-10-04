<div x-data="{ 
    lightboxOpen: false, 
    activeImg: '', 
    activeTitle: '',
    openLightbox(src, title = '') {
        this.activeImg = src;
        this.activeTitle = title;
        this.lightboxOpen = true;
    }
}" 
@keydown.escape.window="lightboxOpen = false" 
class="bg-slate-950 min-h-screen text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-10">

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
                    <i class="fa-solid fa-location-dot text-amber-400"></i> {{ $scheme->location }}, {{ $scheme->city?->name ?? $scheme->city }}
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
                        <span class="text-xl font-black text-slate-200">{{ $scheme->city?->name ?? $scheme->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Map Integration Button Box -->
            @php
                $cityName = $scheme->city?->name ?? $scheme->city ?? '';
                $mapUrl = !empty($scheme->google_map_url) 
                    ? $scheme->google_map_url 
                    : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode(($scheme->location ?? '') . ', ' . $cityName);
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

        <!-- Master Plan & Photo Gallery Section -->
        @php
            $gallery = is_array($scheme->gallery_images) ? array_filter($scheme->gallery_images) : [];
        @endphp

        @if($scheme->master_plan_map || count($gallery) > 0)
            <div class="space-y-6">
                <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center gap-2.5">
                            <i class="fa-solid fa-images text-amber-400"></i> Master Plan & Society Gallery
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Visual layout and on-ground development snapshots of {{ $scheme->name }}.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Feature 1: Master Plan / Layout Map -->
                    @if($scheme->master_plan_map)
                        <div class="{{ count($gallery) > 0 ? 'lg:col-span-5' : 'lg:col-span-12' }} bg-slate-900/90 border border-slate-800 rounded-3xl p-5 space-y-3 flex flex-col justify-between shadow-xl">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                                        <i class="fa-solid fa-map"></i> Official Master Plan
                                    </span>
                                    <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded-md font-semibold">Click to Zoom</span>
                                </div>
                                <div 
                                    @click="openLightbox('{{ asset('public/'.$scheme->master_plan_map) }}', '{{ addslashes($scheme->name) }} - Official Master Plan')" 
                                    class="relative group rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 aspect-[4/3] cursor-pointer"
                                >
                                    <img src="{{ asset('public/'.$scheme->master_plan_map) }}" alt="{{ $scheme->name }} Master Plan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-slate-950/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <span class="px-4 py-2 bg-amber-500 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg flex items-center gap-1.5">
                                            <i class="fa-solid fa-up-right-and-down-left-from-center"></i> View High-Res Map
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 italic text-center">Approved society map showing blocks, roads, and amenity plots.</p>
                        </div>
                    @endif

                    <!-- Feature 2: Development Gallery Grid -->
                    @if(count($gallery) > 0)
                        <div class="{{ $scheme->master_plan_map ? 'lg:col-span-7' : 'lg:col-span-12' }} bg-slate-900/90 border border-slate-800 rounded-3xl p-5 space-y-4 shadow-xl">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-camera"></i> Development Photos & Amenities
                                </span>
                                <span class="text-xs text-slate-400 font-semibold">{{ count($gallery) }} Photos</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 max-h-[460px] overflow-y-auto pr-1">
                                @foreach($gallery as $index => $photo)
                                    <div 
                                        @click="openLightbox('{{ asset('public/'.$photo) }}', '{{ addslashes($scheme->name) }} - Site Photo #{{ $index + 1 }}')" 
                                        class="relative group rounded-xl overflow-hidden border border-slate-800 bg-slate-950 aspect-[4/3] cursor-pointer"
                                    >
                                        <img src="{{ asset('public/'.$photo) }}" alt="Society Photo" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <i class="fa-solid fa-magnifying-glass-plus text-amber-400 text-lg"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

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

    <!-- Fullscreen Lightbox Modal (Alpine.js) -->
    <div 
        x-show="lightboxOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md" 
        style="display: none;"
    >
        <div @click.outside="lightboxOpen = false" class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center">
            <!-- Modal Header / Controls -->
            <div class="w-full flex items-center justify-between pb-3 text-slate-300">
                <span class="text-sm font-semibold truncate" x-text="activeTitle"></span>
                <div class="flex items-center gap-3">
                    <a :href="activeImg" target="_blank" download class="text-xs text-amber-400 hover:text-amber-300 font-bold bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-800">
                        <i class="fa-solid fa-download"></i> Open Original
                    </a>
                    <button @click="lightboxOpen = false" class="text-slate-400 hover:text-white text-2xl font-bold bg-slate-900 w-8 h-8 rounded-lg border border-slate-800 flex items-center justify-center">&times;</button>
                </div>
            </div>

            <!-- Image View Box -->
            <div class="w-full rounded-2xl overflow-hidden border border-slate-800 bg-slate-900 flex items-center justify-center max-h-[80vh]">
                <img :src="activeImg" :alt="activeTitle" class="w-full h-auto max-h-[80vh] object-contain">
            </div>
        </div>
    </div>
</div>