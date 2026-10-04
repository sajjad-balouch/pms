<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Header & Back Link -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>🏘️</span> Manage Housing Schemes & Towns
            </h2>
            <p class="text-xs text-gray-400 mt-1">Apni tamam housing schemes, maps aur development galleries yahan se manage karein.</p>
        </div>
        <a href="{{ route('town_owner.dashboard') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">
            <span>←</span> Back to Dashboard
        </a>
    </div>

    <!-- Success Flash Message -->
    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
        </div>
    @endif

    <!-- Add / Edit Town Form Card -->
    <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-6">
        <div class="border-b border-gray-800/80 pb-4 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>{{ $editingTownId ? '✏️' : '✨' }}</span> 
                    {{ $editingTownId ? 'Edit Housing Scheme & Media' : 'Add New Project / Housing Scheme' }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $editingTownId ? 'Scheme info, master plan map aur gallery images yahan update karein.' : 'Nayi housing scheme register karne ke liye basic details darj karein.' }}
                </p>
            </div>
            @if($editingTownId)
                <button type="button" wire:click="cancelEdit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl transition-colors">
                    ✕ Cancel Edit
                </button>
            @endif
        </div>
        
        <form wire:submit="createTown" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Town Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Town Name *</label>
                    <input type="text" wire:model="name" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. Green Valley City" required />
                    @error('name') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Searchable City Dropdown -->
                <div x-data="{ open: false }" class="relative" wire:key="town-city-selector-{{ $editingTownId ?? 'create' }}">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">City *</label>
                    
                    <div 
                        @click="open = !open" 
                        class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm cursor-pointer flex justify-between items-center hover:border-gray-700 transition-colors focus:outline-none"
                    >
                        <span class="{{ $selected_city_name ? 'text-white font-medium' : 'text-gray-500' }}">
                            {{ $selected_city_name ?: 'Select a City...' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>

                    <div 
                        x-show="open" 
                        @click.outside="open = false" 
                        x-transition 
                        class="absolute z-50 left-0 right-0 mt-1 bg-gray-900 border border-gray-800 rounded-xl shadow-2xl p-2 space-y-2 max-h-60 overflow-y-auto"
                        style="display: none;"
                    >
                        <input 
                            type="text" 
                            wire:model.live.debounce.200ms="city_search" 
                            placeholder="Search city..." 
                            class="w-full px-3 py-1.5 text-xs bg-gray-950 border border-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                        
                        <div class="divide-y divide-gray-800/80">
                            @forelse($cities as $c)
                                <button 
                                    type="button" 
                                    wire:click="selectCity({{ $c->id }}, '{{ addslashes($c->name) }}')" 
                                    @click="open = false" 
                                    class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors flex justify-between items-center {{ $city_id == $c->id ? 'bg-indigo-600/30 text-indigo-400 font-bold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                                >
                                    <span>{{ $c->name }}</span>
                                    @if($city_id == $c->id)
                                        <span class="text-indigo-400 font-bold">✓</span>
                                    @endif
                                </button>
                            @empty
                                <div class="px-3 py-2 text-xs text-gray-500">No active city found.</div>
                            @endforelse
                        </div>
                    </div>

                    @error('city_id') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Location / Address -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Location / Address *</label>
                    <input type="text" wire:model="location" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. Main Ring Road" required />
                    @error('location') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Google Map URL Field -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Google Map Location Link</label>
                    <input type="url" wire:model="google_map_url" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="https://maps.app.goo.gl/..." />
                    @error('google_map_url') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Total Area -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Area</label>
                    <input type="text" wire:model="total_area" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. 150 Kanal" />
                </div>

                <!-- NOC Number -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">NOC Number / Approval</label>
                    <input type="text" wire:model="noc_number" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. LDA/NOC/2026-10" />
                </div>
            </div>

            <!-- OPTIONAL MEDIA SECTION (Shown on Edit Mode) -->
            @if($editingTownId)
                <div class="border-t border-gray-800/80 pt-6 mt-6 space-y-6">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🗺️</span>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-amber-400">Town Master Plan & Gallery (Optional)</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-950/60 p-5 rounded-2xl border border-gray-800">
                        <!-- Feature 1: Master Plan / Map -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-300">
                                Official Town Master Map (Plan Layout)
                            </label>
                            
                            @if($existing_master_plan_map)
                                <div class="relative group rounded-xl overflow-hidden border border-gray-700 bg-gray-900 w-full max-w-xs">
                                    <img src="{{ asset('public/'.$existing_master_plan_map) }}" alt="Town Master Plan" class="w-full h-44 object-cover">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                                        <a href="{{ asset('public/'.$existing_master_plan_map) }}" target="_blank" class="px-2.5 py-1.5 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-700 font-semibold">
                                            🔍 View Full
                                        </a>
                                        <button type="button" wire:click="deleteMasterPlanMap" wire:confirm="Are you sure you want to remove the town master plan?" class="px-2.5 py-1.5 bg-rose-600 text-white text-xs rounded-lg hover:bg-rose-500 font-semibold">
                                            🗑️ Remove
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <input type="file" wire:model="new_master_plan_map" accept="image/*" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500/10 file:text-amber-400 hover:file:bg-amber-500/20 cursor-pointer">
                            <span class="text-[11px] text-gray-500 block">Naya master plan map upload karne ke liye file select karein (Max: 5MB).</span>
                            @error('new_master_plan_map') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Feature 2: Gallery Images (With Direct Remove & Clear All Options) -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-300">
                                    Scheme Gallery (Development & Site Photos)
                                </label>
                                @if(!empty($existing_gallery_images))
                                    <button type="button" wire:click="deleteAllGalleryImages" wire:confirm="Are you sure you want to remove all gallery images?" class="text-[11px] text-rose-400 hover:text-rose-300 font-bold bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded-lg transition-colors">
                                        🗑️ Remove All
                                    </button>
                                @endif
                            </div>

                            <!-- Existing Gallery Photos Grid -->
                            @if(!empty($existing_gallery_images))
                                <div class="grid grid-cols-3 gap-2.5 max-h-48 overflow-y-auto p-1 bg-gray-900/60 rounded-xl border border-gray-800">
                                    @foreach($existing_gallery_images as $idx => $img)
                                        <div class="relative group rounded-lg overflow-hidden border border-gray-700 aspect-video bg-gray-900">
                                            <img src="{{ asset('public/'.$img) }}" alt="Gallery Image" class="w-full h-full object-cover">
                                            
                                            <!-- Action Overlay on Hover -->
                                            <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-1.5 transition-opacity">
                                                <a href="{{ asset('public/'.$img) }}" target="_blank" class="p-1 bg-gray-800 text-white rounded text-[11px] hover:bg-gray-700" title="View Full">
                                                    🔍
                                                </a>
                                                <button type="button" wire:click="deleteGalleryImage({{ $idx }})" wire:confirm="Delete this photo?" class="p-1 bg-rose-600 text-white rounded text-[11px] hover:bg-rose-500" title="Remove Photo">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <input type="file" wire:model="new_gallery_images" multiple accept="image/*" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                            <span class="text-[11px] text-gray-500 block">Ek sath multiple images select karein (Parks, Roads, Main Gate waghera).</span>
                            @error('new_gallery_images.*') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit & Cancel Buttons -->
            <div class="pt-2 flex items-center gap-3">
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 {{ $editingTownId ? 'bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border-amber-500/30' : 'bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border-indigo-500/30' }} border font-bold py-2.5 px-6 rounded-xl text-sm shadow-sm transition-all duration-200 disabled:opacity-50">
                    <span wire:loading.remove wire:target="createTown">{{ $editingTownId ? 'Update Housing Scheme & Media' : 'Save Housing Scheme' }}</span>
                    <span wire:loading wire:target="createTown" class="animate-pulse">Saving Scheme...</span>
                </button>

                @if($editingTownId)
                    <button type="button" wire:click="cancelEdit" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm transition-all duration-200">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Registered Towns Table -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800/80">
            <h3 class="text-lg font-extrabold text-white">My Schemes List</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Town Name</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">City & Location</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Plan / Gallery</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Area</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">NOC</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($towns as $town)
                        @php
                            $cityName = $town->city?->name ?? $town->city ?? 'N/A';
                            $mapUrl = !empty($town->google_map_url) 
                                ? $town->google_map_url 
                                : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode(($town->location ?? '') . ', ' . $cityName);
                            $galleryCount = is_array($town->gallery_images) ? count($town->gallery_images) : 0;
                        @endphp
                        <tr wire:key="town-row-{{ $town->id }}" class="hover:bg-gray-800/40 transition-colors {{ $editingTownId === $town->id ? 'bg-amber-500/10 border-l-4 border-l-amber-500' : '' }}">
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">{{ $town->name }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">
                                <span class="text-amber-400 font-medium">{{ $cityName }}</span> - {{ $town->location }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                @if($town->master_plan_map)
                                    <a href="{{ asset($town->master_plan_map) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-lg">
                                        🗺️ Plan
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-600">No Map</span>
                                @endif

                                @if($galleryCount > 0)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-lg">
                                        🖼️️ {{ $galleryCount }} Photos
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->total_area ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ $town->noc_number ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('town_owner.plots', $town->id) }}" class="inline-flex items-center gap-1 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>📍</span> Plots
                                </a>
                                <button wire:click="editTown({{ $town->id }})" class="inline-flex items-center gap-1 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>✏️</span> Edit
                                </button>
                                <button wire:click="deleteTown({{ $town->id }})" wire:confirm="Are you sure you want to delete this town?" class="inline-flex items-center gap-1 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>🗑️</span> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <p class="text-base font-semibold">No schemes added yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($towns->hasPages())
            <div class="p-4 border-t border-gray-800/80 bg-gray-950/30">
                {{ $towns->links() }}
            </div>
        @endif
    </div>

</div>