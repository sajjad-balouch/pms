<div>
    <!-- Hero Section -->
    <section class="relative min-h-0 sm:min-h-[80vh] flex items-center justify-center pt-6 pb-12 sm:pt-12 sm:pb-20 px-3 sm:px-4 overflow-hidden">
        <!-- Background Decorative Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[350px] sm:w-[600px] h-[350px] sm:h-[600px] bg-amber-500/10 rounded-full blur-[100px] sm:blur-[140px] pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] bg-blue-500/10 rounded-full blur-[90px] sm:blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 max-w-6xl mx-auto text-center space-y-4 sm:space-y-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 sm:px-4 sm:py-1.5 rounded-full bg-slate-800/80 border border-slate-700/80 text-amber-400 text-[11px] sm:text-xs font-semibold uppercase tracking-wider shadow-inner">
                <i class="fa-solid fa-sparkles text-amber-400"></i> Verified Land Registry & Agent Listings
            </span>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight">
                Find Plots, Houses & Shops <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500 bg-clip-text text-transparent">For Sale & Rent</span>
            </h1>

            <p class="text-slate-400 text-xs sm:text-base max-w-2xl mx-auto font-normal px-2">
                Explore direct verified listings from trusted property agents, town owners, and societies.
            </p>

            <!-- Search Filter Card (Mobile: 2 Columns Per Row, Desktop: 3 Columns) -->
            <div class="bg-slate-800/95 backdrop-blur-xl border border-slate-700/80 p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl text-left max-w-5xl mx-auto">
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-4">
                    
                    <!-- Purpose (Sale / Rent) -->
                    <div>
                        <label class="block text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 sm:mb-1.5 truncate">Purpose</label>
                        <select wire:model.live="purpose" class="w-full bg-slate-900/90 border border-slate-700 text-slate-200 rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">All (Sale/Rent)</option>
                            <option value="for_sale">For Sale</option>
                            <option value="for_rent">For Rent</option>
                        </select>
                    </div>

                    <!-- Property Type (Plot, House, Shop) -->
                    <div>
                        <label class="block text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 sm:mb-1.5 truncate">Property Type</label>
                        <select wire:model.live="propertyType" class="w-full bg-slate-900/90 border border-slate-700 text-slate-200 rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">All Types</option>
                            <option value="plot">Plot</option>
                            <option value="house">House</option>
                            <option value="shop">Shop / Commercial</option>
                        </select>
                    </div>

                    <!-- Scheme / Town Selector -->
                    <div>
                        <label class="block text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 sm:mb-1.5 truncate">Housing Scheme</label>
                        <select wire:model.live="selectedTown" class="w-full bg-slate-900/90 border border-slate-700 text-slate-200 rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none truncate">
                            <option value="">All Schemes</option>
                            @foreach($towns as $town)
                                <option value="{{ $town->id }}">{{ $town->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Agent Wise Filter -->
                    <div>
                        <label class="block text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 sm:mb-1.5 truncate">Filter By Agent</label>
                        <select wire:model.live="selectedAgent" class="w-full bg-slate-900/90 border border-slate-700 text-slate-200 rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none truncate">
                            <option value="">All Agents</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Size Filter -->
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 sm:mb-1.5 truncate">Property Size</label>
                        <select wire:model.live="size" class="w-full bg-slate-900/90 border border-slate-700 text-slate-200 rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">Any Size</option>
                            <option value="5">5 Marla</option>
                            <option value="10">10 Marla</option>
                            <option value="1">1 Kanal</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="col-span-2 sm:col-span-1 flex items-end pt-1 sm:pt-0">
                        <button wire:click="applyFilter" wire:loading.attr="disabled" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-2.5 sm:py-2.5 px-4 rounded-lg sm:rounded-xl transition-all shadow-lg shadow-amber-500/20 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                            <span wire:loading.remove><i class="fa-solid fa-magnifying-glass"></i> Search Properties</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Searching...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Property Showcase Section -->
    <section id="results-section" x-data @scroll-to-results.window="document.getElementById('results-section').scrollIntoView({ behavior: 'smooth' })" class="py-12 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-12 gap-4">
            <div>
                <span class="text-amber-400 text-xs font-bold uppercase tracking-widest">Marketplace Inventory</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">Available Properties ({{ count($properties) }})</h2>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <button wire:click="$set('filterStatus', 'all')" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs font-semibold border transition-all {{ $filterStatus === 'all' ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-800 text-slate-400 border-slate-700' }}">All</button>
                <button wire:click="$set('filterStatus', 'available')" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs font-semibold border transition-all {{ $filterStatus === 'available' ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-800 text-slate-400 border-slate-700' }}">Available Only</button>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @forelse($properties as $property) 
            <div class="bg-slate-800/60 border border-slate-700/60 rounded-3xl overflow-hidden hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1.5 group shadow-xl">
                <div class="relative h-48 sm:h-52 bg-slate-900 overflow-hidden">
                    @if(!empty($property->images[0]))
                        <img src="{{ asset('public/'.$property->images[0]) }}" alt="Property Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90">
                    @else
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1073&auto=format&fit=crop" alt="Property Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90">
                    @endif
                    
                    <!-- Badges -->
                    <div class="absolute top-3.5 left-3.5 flex gap-2">
                        <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider shadow-md bg-amber-500 text-slate-950">
                            {{ $property->purpose ?? 'Sale' }}
                        </span>
                        <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider shadow-md {{ $property->status === 'available' ? 'bg-emerald-500/90 text-slate-950' : 'bg-rose-500/90 text-white' }}">
                            {{ $property->status }}
                        </span>
                    </div>

                    <!-- Google Maps Navigation Button -->
                    @php
                        $cityName = $property->city?->name ?? $property->town?->city?->name ?? $property->city ?? '';
                        $mapUrl = !empty($property->google_map_url) 
                            ? $property->google_map_url 
                            : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode(($property->location ?? '') . ', ' . $cityName);
                    @endphp
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" 
                       title="Navigate via Google Maps"
                       class="absolute top-3.5 right-3.5 bg-slate-950/80 hover:bg-emerald-600 text-emerald-400 hover:text-white p-2 rounded-full backdrop-blur-md border border-slate-700/80 transition-all duration-300 shadow-lg group/map">
                        <i class="fa-solid fa-location-dot text-xs group-hover/map:scale-110 transition-transform"></i>
                    </a>
                </div>

                <div class="p-5 sm:p-6 space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[11px] sm:text-xs text-amber-400 font-semibold uppercase tracking-wider">{{ $property->town->name ?? 'Prime Location' }}</span>
                            <h3 class="text-lg sm:text-xl font-bold text-white mt-0.5 line-clamp-1">{{ ucfirst($property->property_type ?? 'Property') }} # {{ $property->property_number ?? $property->title ?? $property->id }}</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-base sm:text-lg font-extrabold text-amber-400">PKR {{ number_format($property->price ?? 0) }}</span>
                            @if(($property->purpose ?? '') === 'rent')
                                <span class="block text-[10px] text-slate-400">/ Month</span>
                            @endif
                        </div>
                    </div>

                    <!-- Location Bar -->
                    <div class="flex items-center justify-between text-xs text-slate-400 bg-slate-900/50 px-3 py-2 rounded-xl border border-slate-700/40">
                        <span class="truncate max-w-[170px]" title="{{ $property->location ?? '' }}, {{ $cityName }}">
                            <i class="fa-solid fa-map-pin text-rose-400 mr-1"></i>
                            {{ $property->location ?? $cityName ?? 'Location N/A' }}
                        </span>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" 
                           class="text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1 transition-colors shrink-0">
                            <i class="fa-solid fa-diamond-turn-right text-xs"></i> Navigate
                        </a>
                    </div>

                    <!-- Details & Agent Info -->
                    <div class="grid grid-cols-2 gap-3 py-2.5 border-y border-slate-700/50 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-ruler-combined text-slate-500"></i>
                            <span class="truncate">{{ $property->area_size ?? $property->size ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-2 cursor-pointer hover:text-amber-400" wire:click="openUnlockModal({{ $property->agent_id ?? $property->user->id ?? 0 }})">
                            <i class="fa-solid fa-user-tie text-amber-400"></i>
                            <span class="truncate">{{ $property->agent->name ?? $property->user->name ?? 'Agent/Owner' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs text-slate-400"><i class="fa-solid fa-shield-check text-emerald-400"></i> Verified Listing</span>
                        <a href="{{ route('property.details', $property->id) }}" class="text-xs font-bold text-white group-hover:text-amber-400 flex items-center gap-1 transition-colors">
                            Details <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16 bg-slate-800/30 rounded-3xl border border-slate-800">
                <i class="fa-solid fa-building-circle-xmark text-4xl text-slate-600 mb-3"></i>
                <p class="text-slate-400 text-sm">No properties found matching your criteria.</p>
            </div>
            @endforelse
        </div>
    </section>

    <!-- Town Owners & Agents Directory Section -->
    <section class="py-12 bg-slate-900/50 border-y border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-amber-400 text-xs font-bold uppercase tracking-widest">Verified Partners</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">Town Owners & Agents Directory</h2>
                </div>
                <!-- Role Filter Buttons -->
                <div class="flex items-center gap-2">
                    <button wire:click="$set('agentRoleFilter', 'all')" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ ($agentRoleFilter ?? 'all') === 'all' ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-800 text-slate-400 border-slate-700' }}">All</button>
                    <button wire:click="$set('agentRoleFilter', 'town_owner')" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ ($agentRoleFilter ?? '') === 'town_owner' ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-800 text-slate-400 border-slate-700' }}">Town Owners</button>
                    <button wire:click="$set('agentRoleFilter', 'agent')" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ ($agentRoleFilter ?? '') === 'agent' ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-800 text-slate-400 border-slate-700' }}">Agents</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($agents as $person)
                    <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-5 flex flex-col justify-between hover:border-amber-500/40 transition-all shadow-lg group">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="relative">
                                    <img src="{{ $person->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($person->name).'&background=f59e0b&color=0f172a' }}" alt="{{ $person->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-amber-500/30">
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-slate-800 rounded-full"></span>
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="text-base font-bold text-white truncate group-hover:text-amber-400 transition-colors">{{ $person->name }}</h4>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider {{ $person->role === 'town_owner' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' }}">
                                        {{ $person->role === 'town_owner' ? 'Town Owner' : 'Verified Agent' }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 py-3 border-y border-slate-700/50 text-xs text-slate-300">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400"><i class="fa-solid fa-building text-amber-400 mr-1.5"></i> Listed Properties:</span>
                                    <span class="font-bold text-white">{{ $person->properties_count ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400"><i class="fa-solid fa-location-dot text-amber-400 mr-1.5"></i> Primary Region:</span>
                                    <span class="font-medium text-slate-200 truncate max-w-[120px]">{{ $person->city ?? 'Central' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            @if($this->isUnlocked($person->id))
                                <div class="bg-slate-900/90 rounded-xl p-3 border border-emerald-500/30 space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between text-emerald-400 font-semibold text-[11px] mb-1">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-lock-open"></i> Contact Unlocked
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-normal">Active</span>
                                    </div>
                                    <a href="tel:{{ $person->phone }}" class="flex items-center gap-2 text-slate-200 hover:text-amber-400 truncate">
                                        <i class="fa-solid fa-phone text-amber-400 text-[10px]"></i> {{ $person->phone ?? '+92 300 0000000' }}
                                    </a>
                                    <a href="mailto:{{ $person->email }}" class="flex items-center gap-2 text-slate-200 hover:text-amber-400 truncate">
                                        <i class="fa-solid fa-envelope text-amber-400 text-[10px]"></i> {{ $person->email }}
                                    </a>
                                </div>
                            @else
                                <button wire:click="openUnlockModal({{ $person->id }})" class="w-full bg-slate-900 hover:bg-amber-500/10 hover:border-amber-500/50 text-amber-400 border border-slate-700 font-semibold py-2.5 px-4 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-lock text-amber-400"></i> Unlock Contact Details
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-slate-400 text-sm">
                        No agents or town owners found.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Unlock Detail Modal -->
    @if($showUnlockModal && $selectedAgentForUnlock)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-800 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative text-left">
                <button wire:click="$set('showUnlockModal', false)" class="absolute top-5 right-5 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-amber-500/10 border border-amber-500/30 rounded-full flex items-center justify-center mx-auto text-amber-400 text-2xl">
                        <i class="fa-solid fa-wallet"></i>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white">Unlock Contact Profile</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Aap <strong class="text-white">{{ $selectedAgentForUnlock->name }}</strong> ({{ $selectedAgentForUnlock->role === 'town_owner' ? 'Town Owner' : 'Agent' }}) ke direct contact details unlock karne lage hain.
                        </p>
                    </div>

                    <!-- Dynamic DB Fee & User Wallet Balance -->
                    <div class="bg-slate-900/90 p-4 rounded-2xl border border-amber-500/30 text-xs space-y-2">
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Unlock Fee:</span>
                            <span class="font-extrabold text-amber-400">PKR {{ number_format($unlockFee) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Your Current Balance:</span>
                            <span class="font-bold text-white">PKR {{ number_format(auth()->user()->wallet_balance ?? 0) }}</span>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    @if($errorMessage)
                        <div class="p-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-semibold">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <!-- Unlock Action Button -->
                    <div class="pt-2 space-y-3">
                        <button wire:click="confirmUnlock({{ $selectedAgentForUnlock->id }})" wire:loading.attr="disabled" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-amber-500/25 flex items-center justify-center gap-2">
                            <span wire:loading.remove><i class="fa-solid fa-lock-open"></i> Pay PKR {{ number_format($unlockFee) }} & Unlock</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Processing...</span>
                        </button>
                        <button wire:click="$set('showUnlockModal', false)" class="w-full text-slate-400 hover:text-slate-200 text-xs font-semibold py-2">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>