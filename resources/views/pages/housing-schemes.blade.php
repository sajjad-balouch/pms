<div class="bg-slate-950 min-h-screen text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-10">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800 pb-8">
            <div>
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full inline-flex items-center">
                    <i class="fa-solid fa-city mr-1.5 ml-1.5"></i> {{ __('Approved Projectsq') }}
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white mt-3 tracking-tight">{{ __('Housing Schemesq') }}</h1>
                <p class="text-slate-400 text-sm mt-2">{{ __('Explore government-verified LDA, CDA, and RDA approved housing societies.') }}</p>
            </div>
            
            <!-- Search & Filter -->
            <div class="flex items-center gap-3">
                <input type="text" placeholder="{{ __('Search society...') }}" class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500 w-60">
            </div>
        </div>

        <!-- Schemes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($schemes ?? [] as $scheme)
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden hover:border-amber-500/40 transition-all duration-300 shadow-xl group">
                    <div class="relative h-48 bg-slate-800 overflow-hidden">
                        <img src="{{ $scheme->cover_image ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=600' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-3 {{ app()->getLocale() == 'ur' ? 'right-3' : 'left-3' }} bg-slate-950/80 backdrop-blur-md text-amber-400 text-[10px] font-bold px-3 py-1 rounded-full border border-amber-500/20">
                            {{ __($scheme->status ?? 'NOC Approved') }}
                        </span>
                    </div>
                    <div class="p-6 space-y-4">
                        <h3 class="text-xl font-bold text-white group-hover:text-amber-400 transition-colors">{{ $scheme->name ?? 'Park View City' }}</h3>
                        <p class="text-xs text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-amber-400"></i> {{ $scheme->location ?? 'Main Canal Road, Lahore' }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                            <div><span class="text-slate-500">{{ __('Plots') }}:</span> <strong class="text-slate-200">5, 10 Marla, 1 Kanal</strong></div>
                            <div><span class="text-slate-500">{{ __('Payment') }}:</span> <strong class="text-slate-200">{{ __('Easy Installments') }}</strong></div>
                        </div>
                        <a href="#" class="block text-center py-2.5 w-full bg-slate-800 hover:bg-amber-500 hover:text-slate-950 font-bold text-xs rounded-xl transition-all">
                            {{ __('View Layout & Inventory') }}
                        </a>
                    </div>
                </div>
            @empty
                <!-- Sample Card Preview -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden p-6 space-y-4">
                    <div class="h-40 bg-slate-800/50 rounded-2xl flex items-center justify-center text-slate-600">
                        <i class="fa-solid fa-building text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Bahria Town Phase 8</h3>
                    <p class="text-xs text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-amber-400"></i> Rawalpindi / Islamabad
                    </p>
                </div>
            @endforelse
        </div>

    </div>
</div>