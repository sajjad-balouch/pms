<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Flash Alert -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-semibold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                <span>Direct Property Listings & WhatsApp Hub</span>
                <span class="text-xs bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-2.5 py-0.5 rounded-full font-bold">Pro Tools</span>
            </h2>
            <p class="text-sm text-slate-400 mt-1">List houses, shops, plots, and agricultural land, then share via WhatsApp to groups and buyers with 1 click</p>
        </div>

        <button wire:click="create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md transition-all active:scale-95 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Property
        </button>
    </div>

    <!-- Filters Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-800/90 p-4 rounded-2xl shadow-md border border-slate-700/80">
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search location, title, city..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-white placeholder-slate-500">
            <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <select wire:model.live="typeFilter" class="px-4 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-white">
            <option value="">All Property Types</option>
            <option value="house">House</option>
            <option value="apartment">Apartment</option>
            <option value="shop">Shop / Commercial</option>
            <option value="agricultural">Agricultural Land</option>
            <option value="plot">Plot</option>
        </select>

        <select wire:model.live="purposeFilter" class="px-4 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-white">
            <option value="">All Purposes</option>
            <option value="for_sale">For Sale</option>
            <option value="for_rent">For Rent</option>
        </select>
    </div>

    <!-- Properties Table Card -->
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-6 py-4">Property Title</th>
                        <th class="px-6 py-4">Type & Purpose</th>
                        <th class="px-6 py-4">Size & Location</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">WhatsApp Growth Tools</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse ($properties as $property)
                        @php
                            $cityName = $property->city ?? $property->town?->city ?? 'Pakistan';
                            $propertyUrl = url('/property/' . $property->id);
                            $mapUrl = !empty($property->google_map_url) 
                                ? $property->google_map_url 
                                : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($property->location . ', ' .$cityName);

                            // Clean Multi-line WhatsApp Brochure
                            $adText = "🏢 *" . $property->title . "*\n\n"
                                    . "🔹 *Type:* " . ucfirst($property->property_type) . " (" . str_replace('_', ' ', $property->purpose) . ")\n"
                                    . "🔹 *Area Size:* " . $property->area_size . "\n"
                                    . "🔹 *Demand Price:* PKR " . number_format($property->price) . "\n"
                                    . "📍 *Location:* " . $property->location . ", " . $cityName . "\n\n"
                                    . "🗺️ *Google Maps Location:*\n" . $mapUrl . "\n\n"
                                    . "📸 *Photos & Full Details:*\n" . $propertyUrl . "\n\n"
                                    . "👤 *Contact for Deal:* " . Auth::user()->name . "\n"
                                    . "⚡ Powered by AssanZameen.com";

                            // Catalog Style Card
                            $catalogText = "📦 *PROPERTY CATALOG CARD*\n"
                                         . "━━━━━━━━━━━━━━━━━━━━\n"
                                         . "🏠 *{$property->title}*\n"
                                         . "💰 Price: PKR " . number_format($property->price) . "\n"
                                         . "📐 Area: {$property->area_size}\n"
                                         . "📍 Location: {$property->location}, {$cityName}\n"
                                         . "🔗 Visit Link: {$propertyUrl}\n"
                                         . "━━━━━━━━━━━━━━━━━━━━\n"
                                         . "Dealer: " . Auth::user()->name;
                        @endphp
                        <tr wire:key="property-row-{{ $property->id }}" class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ $property->title }}</div>
                                @if($property->town)
                                    <div class="text-xs text-indigo-400 font-medium mt-0.5">Scheme: {{ $property->town->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 capitalize">
                                <span class="font-semibold text-slate-200">{{ $property->property_type }}</span>
                                <div class="text-xs text-slate-400">({{ str_replace('_', ' ', $property->purpose) }})</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-200">{{ $property->area_size }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ $property->location }}, 
                                    <span class="text-amber-400 font-medium">
                                        {{ $cityName }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-black text-emerald-400">
                                Rs. {{ number_format($property->price) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold capitalize
                                    {{ $property->status === 'available' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $property->status }}
                                </span>
                            </td>
                            
                            <!-- WhatsApp Tools Grid -->
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5 flex-wrap justify-center max-w-xs" x-data>
                                    
                                    <!-- 1. Direct Share Brochure -->
                                    <button type="button"
                                            @click="shareListing({{ json_encode($adText) }})"
                                            title="Share Brochure on WhatsApp"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition shadow-sm cursor-pointer">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>Share</span>
                                    </button>

                                    <!-- 2. Copy Ad Text for Dealer Groups -->
                                    <button type="button"
                                            @click="navigator.clipboard.writeText({{ json_encode($adText) }}); alert('Property advertisement copied! Ready to paste in dealer groups.')"
                                            title="Copy Ad for WhatsApp Groups"
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-slate-900 hover:bg-slate-700 text-amber-400 border border-slate-700 rounded-lg text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-copy text-xs"></i>
                                        <span>Group Ad</span>
                                    </button>

                                    <!-- 3. Send Location Pin Drop -->
                                    <button type="button"
                                            wire:click="sendLocationPin({{ $property->id }})"
                                            title="Send Google Maps Pin to Client"
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 rounded-lg text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-location-dot text-xs text-rose-400"></i>
                                        <span>Location</span>
                                    </button>

                                    <!-- 4. Client Follow-Up Deal Closing Modal -->
                                    <button type="button"
                                            wire:click="openFollowUpModal({{ $property->id }})"
                                            title="Send Instant Follow-up to Lead (Visit/Token/Offer)"
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-purple-600/20 hover:bg-purple-600 text-purple-400 hover:text-white border border-purple-500/30 rounded-lg text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-user-clock text-xs"></i>
                                        <span>Follow-Up</span>
                                    </button>

                                    <!-- 5. Catalog Card Copy -->
                                    <button type="button"
                                            @click="navigator.clipboard.writeText({{ json_encode($catalogText) }}); alert('Business catalog card copied to clipboard.')"
                                            title="Copy WhatsApp Business Catalog Format"
                                            class="inline-flex items-center px-2 py-1 bg-slate-900 hover:bg-slate-700 text-slate-300 border border-slate-700 rounded-lg text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-layer-group text-xs text-indigo-400"></i>
                                    </button>

                                </div>
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $property->id }})" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 cursor-pointer">Edit</button>
                                <button wire:click="delete({{ $property->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="text-xs font-semibold text-rose-400 hover:text-rose-300 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">No individual properties listed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700/80">
            {{ $properties->links() }}
        </div>
    </div>

    <!-- Client Follow-Up Modal -->
    @if($isFollowUpModalOpen &&$selectedPropertyForFollowup)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <h3 class="text-base font-bold text-white">Client WhatsApp Follow-Up</h3>
                </div>
                <button wire:click="$set('isFollowUpModalOpen', false)" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <form wire:submit="sendFollowUp" class="p-6 space-y-4">
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-xs text-slate-300 flex items-center justify-between">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Selected Property</span>
                        <span class="font-bold text-amber-400">{{ $selectedPropertyForFollowup->title }}</span>
                    </div>
                    <span class="font-bold text-emerald-400">PKR {{ number_format($selectedPropertyForFollowup->price) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Client Name *</label>
                        <input type="text" wire:model="client_name" placeholder="e.g. Ahmed Raza" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                        @error('client_name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">WhatsApp Number *</label>
                        <input type="text" wire:model="client_phone" placeholder="03001234567" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                        @error('client_phone') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Select Follow-Up Objective</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="$set('followup_type', 'site_visit')"
                                class="p-2.5 rounded-xl border text-[11px] font-bold transition text-center {{ $followup_type === 'site_visit' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            🏡 Site Visit
                        </button>
                        <button type="button" wire:click="$set('followup_type', 'token_reminder')"
                                class="p-2.5 rounded-xl border text-[11px] font-bold transition text-center {{ $followup_type === 'token_reminder' ? 'border-amber-500 bg-amber-500/10 text-amber-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            🤝 Token Offer
                        </button>
                        <button type="button" wire:click="$set('followup_type', 'price_offer')"
                                class="p-2.5 rounded-xl border text-[11px] font-bold transition text-center {{ $followup_type === 'price_offer' ? 'border-indigo-500 bg-indigo-500/10 text-indigo-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            💼 Office Meeting
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Custom Note / Time (Optional)</label>
                    <input type="text" wire:model="custom_note" placeholder="e.g. Meeting at site at 5:00 PM" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('isFollowUpModalOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Send WhatsApp Now</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Main Property Create / Edit Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl border border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">
                    {{ $property_id ? 'Edit Property Details' : 'Add Property Information' }}
                </h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Property Title / Description Name *</label>
                    <input type="text" wire:model="title" placeholder="e.g. 10 Marla Luxury House for Sale" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('title') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Property Type *</label>
                        <select wire:model="property_type" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="house">House</option>
                            <option value="apartment">Apartment</option>
                            <option value="shop">Shop / Commercial</option>
                            <option value="agricultural">Agricultural Land</option>
                            <option value="plot">Plot / Land</option>
                        </select>
                        @error('property_type') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Purpose *</label>
                        <select wire:model="purpose" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="for_sale">For Sale</option>
                            <option value="for_rent">For Rent</option>
                        </select>
                        @error('purpose') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Searchable City Dropdown -->
                    <div x-data="{ open: false }" class="relative" wire:key="city-selector-modal-{{ $property_id ?? 'create' }}">
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">City *</label>
                        
                        <div @click="open = !open" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white cursor-pointer flex justify-between items-center focus:outline-none hover:border-slate-600 transition-colors">
                            <span class="{{ $selected_city_name ? 'text-white font-medium' : 'text-slate-400' }}">
                                {{ $selected_city_name ?: 'Select a City...' }}
                            </span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl p-2 space-y-2 max-h-60 overflow-y-auto" style="display: none;">
                            <input type="text" wire:model.live.debounce.200ms="city_search" placeholder="Search city..." class="w-full px-3 py-1.5 text-xs bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            
                            <div class="divide-y divide-slate-800">
                                @forelse($cities as $c)
                                    <button type="button" wire:click="selectCity({{ $c->id }}, '{{ addslashes($c->name) }}')" @click="open = false" class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors flex justify-between items-center {{ $city_id ==$c->id ? 'bg-indigo-600/30 text-indigo-400 font-bold' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                        <span>{{ $c->name }}</span>
                                        @if($city_id ==$c->id)
                                            <span class="text-indigo-400 font-bold">✓</span>
                                        @endif
                                    </button>
                                @empty
                                    <div class="px-3 py-2 text-xs text-slate-500">No active city found.</div>
                                @endforelse
                            </div>
                        </div>

                        @error('city_id') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Dependent Town Dropdown -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Town / Scheme (Optional)</label>
                        <select wire:model="town_id" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500" {{ !$city_id ? 'disabled' : '' }}>
                            <option value="">None (Individual)</option>
                            @foreach($towns as $town)
                                <option value="{{ $town->id }}">{{ $town->name }}</option>
                            @endforeach
                        </select>
                        @if(!$city_id)
                            <span class="text-[11px] text-slate-500 mt-1 block">Please select a city first.</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Price (PKR) *</label>
                        <input type="number" wire:model="price" placeholder="e.g. 15000000" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('price') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Area / Size *</label>
                        <input type="text" wire:model="area_size" placeholder="e.g. 5 Marla / 2 Acre / 1200 Sq Ft" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('area_size') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Location / Address *</label>
                        <input type="text" wire:model="location" placeholder="e.g. Main Canal Road, Near Mall" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('location') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="available">Available</option>
                            <option value="under_offer">Under Offer</option>
                            <option value="sold">Sold</option>
                            <option value="rented">Rented</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Google Map Location Link (Optional)</label>
                    <div class="relative">
                        <input type="url" wire:model="google_map_url" placeholder="https://maps.app.goo.gl/..." class="w-full pl-9 rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 absolute left-3 top-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    @error('google_map_url') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Existing Images Preview with Delete option -->
                @if(!empty($existing_images))
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Current Saved Photos</label>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                            @foreach($existing_images as $index =>$img)
                                <div class="relative group rounded-lg overflow-hidden border border-slate-700 h-16 bg-slate-950">
                                    <img src="{{ asset('public/' . $img) }}" alt="Preview" class="w-full h-full object-cover">
                                    <button type="button" wire:click="deleteImage({{ $index }})" class="absolute top-0.5 right-0.5 bg-rose-600/90 text-white rounded-md w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Upload New Images</label>
                    <input type="file" wire:model="new_images" multiple class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20">
                    <div wire:loading wire:target="new_images" class="text-xs text-amber-400 mt-1">Uploading images to buffer...</div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Description & Amenities</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Mention gas, water, electricity, facing road, etc."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-xl font-semibold text-xs transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-xs shadow-md transition-all disabled:opacity-50 cursor-pointer">
                        <span wire:loading.remove wire:target="save">Save Property</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Universal Smart WhatsApp Launch & Listener Script -->
    <script>
        function shareListing(message) {
            let encoded = encodeURIComponent(message);
            let isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                window.location.href = `whatsapp://send?text=${encoded}`;
            } else {
                window.location.href = `whatsapp://send?text=${encoded}`;
                setTimeout(function() {
                    if (!document.hidden) {
                        window.open(`https://web.whatsapp.com/send?text=${encoded}`, '_blank');
                    }
                }, 600);
            }
        }

        // Livewire Dispatch Handler for Cross-Device Routing
        document.addEventListener('livewire:navigated', initAgentWhatsAppListener);
        document.addEventListener('DOMContentLoaded', initAgentWhatsAppListener);

        function initAgentWhatsAppListener() {
            if (window._agentWhatsAppListenerAttached) return;
            window._agentWhatsAppListenerAttached = true;

            window.addEventListener('trigger-whatsapp', (event) => {
                const data = event.detail;
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                if (isMobile) {
                    window.location.href = data.appUrl;
                } else {
                    window.location.href = data.appUrl;
                    setTimeout(function() {
                        if (!document.hidden) {
                            window.open(data.webUrl, '_blank');
                        }
                    }, 650);
                }
            });
        }
    </script>
</div>