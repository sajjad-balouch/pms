@php 
    //dd($property);
    // Agar relation load nahi hua toh direct relationship method call karke name nikal lein
    $cityName = $property->city?->name 
        ?? $property->city()->first()?->name 
        ?? $property->town?->city?->name 
        ?? 'N/A';
    $images = is_array($property->images) ? array_values(array_filter($property->images)) : [];
    $firstImg = count($images) > 0 ? asset('public/' . $images[0]) : '';
    $mapUrl = !empty($property->google_map_url) 
        ? $property->google_map_url 
        : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode(($property->location ?? '') . ', ' . $cityName);
@endphp

<div 
    x-data="{ 
        unlockModal: false,
        activeImage: '{{ $firstImg }}',
        lightboxOpen: false,
        lightboxSrc: ''
    }"
    x-on:unlock-success.window="unlockModal = false"
    class="bg-slate-950 min-h-screen text-slate-100 py-10 px-4 sm:px-6 lg:px-8"
>
    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 px-6 py-4 rounded-2xl flex items-center justify-between">
            <span><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-400/60 hover:text-rose-400">&times;</button>
        </div>
    @endif
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-2xl flex items-center justify-between">
            <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
        </div>
    @endif

    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Top Bar Navigation & Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800/80 pb-6">
            <div>
                <a href="{{ url('/') }}" class="text-amber-400 hover:text-amber-300 text-xs font-semibold uppercase tracking-wider inline-flex items-center gap-2 mb-2 transition">
                    <i class="fa-solid fa-arrow-left"></i> Back to Marketplace
                </a>
                <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    {{ $property->title ?? ucfirst($property->property_type) }}
                </h1>
                <p class="text-slate-400 text-sm mt-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-amber-500"></i>
                    <span>{{ $property->location ?? 'Location' }},</span>
                    <span class="text-amber-400 font-bold">{{ $cityName }}</span>
                    @if($property->town)
                        <span class="text-xs bg-slate-900 border border-slate-800 text-slate-300 px-2.5 py-0.5 rounded-full font-medium">
                            Scheme: {{ $property->town->name }}
                        </span>
                    @endif
                </p>
            </div>
            
            <div class="text-left sm:text-right bg-slate-900/90 border border-slate-800 px-5 py-3 rounded-2xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Demand Price</span>
                <span class="text-2xl sm:text-3xl font-black text-amber-400">PKR {{ number_format($property->price ?? 0) }}</span>
            </div>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2-Columns: Images & Details -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Modern Image Gallery Section -->
                <div class="space-y-4">
                    <div class="relative h-[360px] sm:h-[460px] rounded-3xl overflow-hidden border border-slate-800 bg-slate-900 shadow-2xl group">
                        @if(count($images) > 0)
                            <img 
                                src="{{ $firstImg }}" 
                                :src="activeImage" 
                                alt="{{ $property->title }}" 
                                class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105"
                            >
                            <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button 
                                    type="button" 
                                    @click="lightboxSrc = activeImage; lightboxOpen = true" 
                                    class="px-4 py-2 rounded-xl bg-slate-900/90 hover:bg-slate-900 text-amber-400 border border-slate-700 font-bold text-xs shadow-xl flex items-center gap-2 transition"
                                >
                                    <i class="fa-solid fa-expand"></i> View High-Res Image
                                </button>
                            </div>
                        @else
                            <img 
                                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1073&auto=format&fit=crop" 
                                alt="{{ $property->title }}" 
                                class="w-full h-full object-cover"
                            >
                        @endif

                        <!-- Floating Badges -->
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-lg bg-amber-500 text-slate-950">
                                {{ str_replace('_', ' ', $property->purpose) }}
                            </span>
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg bg-slate-900/90 text-slate-200 border border-slate-700">
                                {{ $property->property_type }}
                            </span>
                        </div>

                        @if(count($images) > 1)
                            <div class="absolute bottom-4 right-4 bg-slate-950/80 backdrop-blur-md border border-slate-700/80 px-3 py-1 rounded-xl text-xs font-semibold text-slate-300">
                                <i class="fa-solid fa-images text-amber-400 mr-1.5"></i> {{ count($images) }} Photos
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnails Strip -->
                    @if(count($images) > 1)
                        <div class="flex items-center gap-3 overflow-x-auto pb-2">
                            @foreach($images as $img)
                                <button 
                                    type="button" 
                                    @click="activeImage = '{{ asset('public/'.$img) }}'" 
                                    class="relative shrink-0 w-20 sm:w-24 h-16 sm:h-20 rounded-2xl overflow-hidden border-2 transition-all duration-200"
                                    :class="activeImage === '{{ asset('public/'.$img) }}' ? 'border-amber-400 scale-95 ring-2 ring-amber-400/40' : 'border-slate-800 opacity-70 hover:opacity-100'"
                                >
                                    <img src="{{ asset('public/'.$img) }}" alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Attributes Grid Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 grid grid-cols-2 sm:grid-cols-4 gap-6 shadow-xl">
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold uppercase text-slate-500 tracking-wider block">Property Type</span>
                        <span class="text-base font-bold text-white capitalize">{{ $property->property_type }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold uppercase text-slate-500 tracking-wider block">Area / Size</span>
                        <span class="text-base font-bold text-amber-400">{{ $property->area_size }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold uppercase text-slate-500 tracking-wider block">City</span>
                        <span class="text-base font-bold text-white">{{ $cityName }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold uppercase text-slate-500 tracking-wider block">Listing Purpose</span>
                        <span class="text-base font-bold text-emerald-400 capitalize">{{ str_replace('_', ' ', $property->purpose) }}</span>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4 shadow-xl">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-amber-400"></i> Description & Overview
                    </h3>
                    <div class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                        {{ $property->description ?: 'No detailed description provided for this listing.' }}
                    </div>
                </div>

            </div>

            <!-- Right Column: Sidebar (Location Guidance & Agent Contact) -->
            <div class="space-y-6">

                <!-- Location Map Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl text-center">
                    <div class="w-12 h-12 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-2xl mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-base">On-Ground Location</h4>
                        <p class="text-xs text-slate-400 mt-1">{{ $property->location }}, {{ $cityName }}</p>
                    </div>
                    <a 
                        href="{{ $mapUrl }}" 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20"
                    >
                        <i class="fa-solid fa-diamond-turn-right"></i> Open on Google Maps
                    </a>
                </div>

                <!-- Agent Details Sidebar Card (Locked / Unlocked) -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-6 shadow-2xl relative">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <span class="text-amber-400 text-xs font-bold uppercase tracking-widest">
                            <i class="fa-solid fa-address-card mr-1"></i> Agent Contact Info
                        </span>
                        @if($isUnlocked)
                            <span class="text-[10px] bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold uppercase px-2 py-0.5 rounded-full">
                                Unlocked
                            </span>
                        @endif
                    </div>

                    @if($isUnlocked)
                        <!-- Unlocked Contact State -->
                        <div class="flex items-center gap-4 border-b border-slate-800 pb-6">
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl font-bold">
                                {{ substr($property->agent->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">{{ $property->agent->name ?? 'Property Agent' }}</h4>
                                <span class="text-xs text-slate-400 capitalize block">{{ $property->agent->role ?? 'Agent' }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if(!empty($property->agent->phone))
                                <a href="tel:{{ $property->agent->phone }}" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3 px-4 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20">
                                    <i class="fa-solid fa-phone"></i> Call {{ $property->agent->phone }}
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $property->agent->phone) }}?text=Hi, I unlocked your property #{{ $property->id }}" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-4 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                                    <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp Direct
                                </a>
                            @else
                                <p class="text-xs text-slate-400 text-center py-2">Phone number not listed on agent profile.</p>
                            @endif
                        </div>
                    @else
                        <!-- Locked Contact State -->
                        <div class="relative rounded-2xl border border-slate-800 bg-slate-950 p-6 text-center space-y-4 overflow-hidden">
                            <div class="filter blur-sm select-none opacity-40 space-y-2 pointer-events-none">
                                <div class="w-12 h-12 rounded-full bg-slate-800 mx-auto"></div>
                                <div class="h-4 bg-slate-800 rounded w-2/3 mx-auto"></div>
                                <div class="h-3 bg-slate-800 rounded w-1/2 mx-auto"></div>
                                <div class="h-10 bg-slate-800 rounded-xl w-full mt-4"></div>
                            </div>

                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/90 rounded-2xl p-4 z-10">
                                <div class="w-12 h-12 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-lg mb-2">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <h4 class="text-white font-bold text-sm mb-1">Direct Contact Protected</h4>
                                <p class="text-xs text-slate-400 mb-4 max-w-[220px]">Pay a small fee from your wallet to instantly unlock agent phone & WhatsApp.</p>
                                
                                @guest
                                    <a href="{{ route('login') }}" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3 px-4 rounded-xl transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 text-xs sm:text-sm">
                                        <i class="fa-solid fa-right-to-bracket"></i> Login to Unlock
                                    </a>
                                @else
                                    <button 
                                        type="button" 
                                        @click="unlockModal = true" 
                                        class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold py-3 px-4 rounded-xl transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer"
                                    >
                                        <i class="fa-solid fa-key"></i> Unlock Contact (PKR {{ number_format($unlockFee) }})
                                    </button>
                                @endguest
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

    <!-- Unlock Fee Modal (Alpine Driven - Zero Morphing Conflict) -->
    <div 
        x-show="unlockModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md" 
        style="display: none;"
    >
        <div @click.outside="unlockModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl relative">
            <button type="button" @click="unlockModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="text-center space-y-3">
                <div class="w-16 h-16 bg-amber-500/20 border border-amber-500/40 text-amber-400 rounded-2xl mx-auto flex items-center justify-center text-2xl shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-extrabold text-white">Unlock Contact Details</h3>
                <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                    To connect directly with the verified agent, a small fee of 
                    <strong class="text-amber-400 font-bold">PKR {{ number_format($unlockFee) }}</strong> will be deducted from your wallet.
                </p>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-2">
                <div class="flex justify-between text-xs text-slate-400">
                    <span>Property ID:</span>
                    <span class="font-bold text-slate-200">#{{ $property->id }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-400">
                    <span>Unlock Fee:</span>
                    <span class="font-bold text-amber-400">PKR {{ number_format($unlockFee) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" @click="unlockModal = false" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-3 rounded-xl text-sm transition">
                    Cancel
                </button>
                <button 
                    type="button" 
                    wire:click="processUnlock" 
                    wire:loading.attr="disabled"
                    class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3 rounded-xl text-sm transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="processUnlock">Yes, Pay & Unlock</span>
                    <span wire:loading wire:target="processUnlock"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Fullscreen Lightbox Modal (Alpine.js) -->
    <div 
        x-show="lightboxOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/95 backdrop-blur-md" 
        style="display: none;"
    >
        <div @click.outside="lightboxOpen = false" class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center">
            <div class="w-full flex items-center justify-between pb-3 text-slate-300">
                <span class="text-sm font-semibold truncate">{{ $property->title }}</span>
                <div class="flex items-center gap-3">
                    <a :href="lightboxSrc" target="_blank" class="text-xs text-amber-400 hover:text-amber-300 font-bold bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Full
                    </a>
                    <button @click="lightboxOpen = false" class="text-slate-400 hover:text-white text-2xl font-bold bg-slate-900 w-8 h-8 rounded-lg border border-slate-800 flex items-center justify-center">
                        &times;
                    </button>
                </div>
            </div>

            <div class="w-full rounded-2xl overflow-hidden border border-slate-800 bg-slate-900 flex items-center justify-center max-h-[80vh]">
                <img :src="lightboxSrc" alt="Full Image" class="w-full h-auto max-h-[80vh] object-contain">
            </div>
        </div>
    </div>

</div>