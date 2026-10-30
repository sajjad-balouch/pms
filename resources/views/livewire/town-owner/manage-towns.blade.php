<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Header & Back Link -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>🏘️</span> Manage Housing Schemes & Towns
            </h2>
            <p class="text-xs text-gray-400 mt-1">Apni tamam housing schemes, maps aur development galleries yahan se manage karein.</p>
        </div>
        <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">
            <span>←</span> Back to Dashboard
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span>⛔</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-rose-400/60 hover:text-rose-400">&times;</button>
        </div>
    @endif

    <!-- Add / Edit Town Form Card -->
    <div x-on:town-form-opened.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
         class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border {{ $editingTownId ? 'border-amber-500/40' : 'border-gray-800/80' }} shadow-2xl space-y-6 scroll-mt-6">
        <div class="border-b border-gray-800/80 pb-4 flex justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>{{ $editingTownId ? '✏️' : '✨' }}</span>
                    {{ $editingTownId ? 'Edit Housing Scheme & Media' : 'Add New Project / Housing Scheme' }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $editingTownId ? 'Scheme info, master plan map aur gallery images yahan update karein.' : 'Nayi housing scheme ki details, map aur gallery yahan darj karein.' }}
                </p>
            </div>
            @if ($editingTownId)
                <button type="button" wire:click="cancelEdit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl transition-colors whitespace-nowrap">
                    ✕ Cancel Edit
                </button>
            @endif
        </div>

        <form wire:submit="saveTown" class="space-y-6">
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

                    <button type="button" @click="open = !open; if (open) $nextTick(() => $refs.citySearch.focus())"
                        class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm cursor-pointer flex justify-between items-center hover:border-gray-700 transition-colors focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <span class="{{ $selected_city_name ? 'text-white font-medium' : 'text-gray-500' }}">
                            {{ $selected_city_name ?: 'Select a City...' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" @keydown.escape.window="open = false" x-transition
                        class="absolute z-50 left-0 right-0 mt-1 bg-gray-900 border border-gray-800 rounded-xl shadow-2xl p-2 space-y-2 max-h-60 overflow-y-auto"
                        style="display: none;">
                        <input type="text" x-ref="citySearch" wire:model.live.debounce.250ms="city_search" placeholder="Search city..."
                            class="w-full px-3 py-1.5 text-xs bg-gray-950 border border-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />

                        <div class="divide-y divide-gray-800/80">
                            @forelse ($cities as $c)
                                <button type="button" wire:key="city-{{ $c->id }}" wire:click="selectCity({{ $c->id }})" @click="open = false"
                                    class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors flex justify-between items-center {{ $city_id == $c->id ? 'bg-indigo-600/30 text-indigo-400 font-bold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                                    <span>{{ $c->name }}</span>
                                    @if ($city_id == $c->id)
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
                    @error('total_area') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- NOC Number -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">NOC Number / Approval</label>
                    <input type="text" wire:model="noc_number" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. LDA/NOC/2026-10" />
                    @error('noc_number') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Late fee setting -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Late Fee % per month</label>
                    <input type="number" min="0" max="20" step="0.5" wire:model="late_fee_percent" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. 2" />
                    <span class="text-[11px] text-gray-500 block mt-1">Late qist par har mahine ka itna % surcharge suggest hoga (wusooli ke waqt badla ya maaf kiya ja sakta hai). 0 = band.</span>
                    @error('late_fee_percent') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- MEDIA SECTION (available on create and edit) -->
            <div class="border-t border-gray-800/80 pt-6 space-y-6">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🗺️</span>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-amber-400">Town Master Plan & Gallery (Optional)</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-950/60 p-5 rounded-2xl border border-gray-800">
                    <!-- Master Plan / Map -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-300">
                            Official Town Master Map (Plan Layout)
                        </label>

                        @if ($editingTown?->master_plan_map)
                            <div class="relative group rounded-xl overflow-hidden border border-gray-700 bg-gray-900 w-full max-w-xs">
                                <img src="{{ $mediaUrl($editingTown->master_plan_map) }}" alt="Town Master Plan" loading="lazy" class="w-full h-44 object-cover">
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                                    <a href="{{ $mediaUrl($editingTown->master_plan_map) }}" target="_blank" class="px-2.5 py-1.5 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-700 font-semibold">
                                        🔍 View Full
                                    </a>
                                    <button type="button" wire:click="deleteMasterPlanMap" wire:confirm="Are you sure you want to remove the town master plan?" class="px-2.5 py-1.5 bg-rose-600 text-white text-xs rounded-lg hover:bg-rose-500 font-semibold">
                                        🗑️ Remove
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- New map preview -->
                        @if ($new_master_plan_map && ! $errors->has('new_master_plan_map') && $new_master_plan_map->isPreviewable())
                            <div class="relative rounded-xl overflow-hidden border border-amber-500/40 bg-gray-900 w-full max-w-xs">
                                <img src="{{ $new_master_plan_map->temporaryUrl() }}" alt="New map preview" class="w-full h-44 object-cover">
                                <span class="absolute top-2 left-2 text-[10px] font-bold bg-amber-500 text-gray-950 px-2 py-0.5 rounded">NEW — save karne par lagega</span>
                                <button type="button" wire:click="$set('new_master_plan_map', null)" class="absolute top-2 right-2 h-6 w-6 bg-black/70 hover:bg-rose-600 text-white rounded-full text-xs">✕</button>
                            </div>
                        @endif

                        <div x-data="{ uploading: false, progress: 0 }"
                             x-on:livewire-upload-start="uploading = true"
                             x-on:livewire-upload-finish="uploading = false; progress = 0"
                             x-on:livewire-upload-error="uploading = false; progress = 0"
                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <input type="file" wire:model="new_master_plan_map" wire:key="map-input-{{ $uploadIteration }}" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500/10 file:text-amber-400 hover:file:bg-amber-500/20 cursor-pointer">
                            <div x-show="uploading" class="mt-2 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden" style="display: none;">
                                <div class="h-full bg-amber-500 transition-all" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                        <span class="text-[11px] text-gray-500 block">
                            {{ $editingTown?->master_plan_map ? 'Naya map select karne par purana map replace ho jayega.' : 'Master plan map upload karein.' }} (JPG/PNG/WEBP, Max: 5MB)
                        </span>
                        @error('new_master_plan_map') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Gallery Images -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-300">
                                Scheme Gallery
                                <span class="text-gray-500 normal-case font-medium">({{ count($existingGallery) }}/{{ $maxGallery }})</span>
                            </label>
                            @if (! empty($existingGallery))
                                <button type="button" wire:click="deleteAllGalleryImages" wire:confirm="Are you sure you want to remove all gallery images?" class="text-[11px] text-rose-400 hover:text-rose-300 font-bold bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded-lg transition-colors">
                                    🗑️ Remove All
                                </button>
                            @endif
                        </div>

                        <!-- Existing Gallery Photos Grid -->
                        @if (! empty($existingGallery))
                            <div class="grid grid-cols-3 gap-2.5 max-h-48 overflow-y-auto p-1 bg-gray-900/60 rounded-xl border border-gray-800">
                                @foreach ($existingGallery as $idx => $img)
                                    <div wire:key="gallery-{{ md5($img) }}" class="relative group rounded-lg overflow-hidden border border-gray-700 aspect-video bg-gray-900">
                                        <img src="{{ $mediaUrl($img) }}" alt="Gallery Image" loading="lazy" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-1.5 transition-opacity">
                                            <a href="{{ $mediaUrl($img) }}" target="_blank" class="p-1 bg-gray-800 text-white rounded text-[11px] hover:bg-gray-700" title="View Full">🔍</a>
                                            <button type="button" wire:click="deleteGalleryImage({{ $idx }})" wire:confirm="Delete this photo?" class="p-1 bg-rose-600 text-white rounded text-[11px] hover:bg-rose-500" title="Remove Photo">🗑️</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- New (not yet saved) gallery previews -->
                        @if (! empty($new_gallery_images) && ! $errors->has('new_gallery_images.*') && ! $errors->has('new_gallery_images'))
                            <div class="grid grid-cols-3 gap-2.5 p-1 bg-amber-500/5 rounded-xl border border-amber-500/30">
                                @foreach ($new_gallery_images as $idx => $image)
                                    @if ($image->isPreviewable())
                                        <div wire:key="new-gallery-{{ $idx }}-{{ $image->getFilename() }}" class="relative rounded-lg overflow-hidden border border-gray-700 aspect-video bg-gray-900">
                                            <img src="{{ $image->temporaryUrl() }}" alt="New image" class="w-full h-full object-cover">
                                            <button type="button" wire:click="removeNewGalleryImage({{ $idx }})" class="absolute top-1 right-1 h-5 w-5 bg-black/70 hover:bg-rose-600 text-white rounded-full text-[10px]">✕</button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <span class="text-[11px] text-amber-400 block">{{ count($new_gallery_images) }} nayi images — save karne par upload hongi.</span>
                        @endif

                        <div x-data="{ uploading: false, progress: 0 }"
                             x-on:livewire-upload-start="uploading = true"
                             x-on:livewire-upload-finish="uploading = false; progress = 0"
                             x-on:livewire-upload-error="uploading = false; progress = 0"
                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <input type="file" wire:model="new_gallery_images" wire:key="gallery-input-{{ $uploadIteration }}" multiple accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                            <div x-show="uploading" class="mt-2 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden" style="display: none;">
                                <div class="h-full bg-indigo-500 transition-all" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                        <span class="text-[11px] text-gray-500 block">Ek sath 10 tak images select karein (Parks, Roads, Main Gate waghera). Max 3MB each.</span>
                        @error('new_gallery_images') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @error('new_gallery_images.*') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Submit & Cancel Buttons -->
            <div class="pt-2 flex items-center gap-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="saveTown,new_master_plan_map,new_gallery_images"
                    class="inline-flex items-center justify-center gap-2 {{ $editingTownId ? 'bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border-amber-500/30' : 'bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border-indigo-500/30' }} border font-bold py-2.5 px-6 rounded-xl text-sm shadow-sm transition-all duration-200 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveTown,new_master_plan_map,new_gallery_images">{{ $editingTownId ? 'Update Housing Scheme & Media' : 'Save Housing Scheme' }}</span>
                    <span wire:loading wire:target="new_master_plan_map,new_gallery_images" class="animate-pulse">Uploading images...</span>
                    <span wire:loading wire:target="saveTown" class="animate-pulse">Saving Scheme...</span>
                </button>

                @if ($editingTownId)
                    <button type="button" wire:click="cancelEdit" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm transition-all duration-200">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Registered Towns Table -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-extrabold text-white">
                My Schemes List
                <span class="ml-1 text-sm font-semibold text-gray-500">({{ $towns->total() }})</span>
            </h3>
            <div class="relative w-full sm:w-72">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name, city, location, NOC..."
                    class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">🔍</span>
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.class="opacity-60" wire:target="search,gotoPage,nextPage,previousPage">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Town Name</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">City & Location</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Plots</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Plan / Gallery</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Area</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">NOC</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($towns as $town)
                        @php
                            $cityName = $town->city ?: 'N/A';
                            $mapUrl = $town->google_map_url
                                ?: 'https://www.google.com/maps/search/?api=1&query=' . urlencode(collect([$town->name, $town->location, $town->city])->filter()->implode(', '));
                            $galleryCount = is_array($town->gallery_images) ? count($town->gallery_images) : 0;
                            $soldRate = $town->plots_count > 0 ? round($town->sold_plots_count / $town->plots_count * 100) : 0;
                        @endphp
                        <tr wire:key="town-row-{{ $town->id }}" class="hover:bg-gray-800/40 transition-colors {{ $editingTownId == $town->id ? 'bg-amber-500/10 border-l-4 border-l-amber-500' : '' }}">
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                {{ $town->name }}
                                <span class="block text-[11px] font-normal text-gray-500">Added {{ $town->created_at?->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">
                                <span class="text-amber-400 font-medium">{{ $cityName }}</span> - {{ $town->location }}
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="block text-[11px] text-sky-400 hover:text-sky-300 mt-0.5">📌 Open in Google Maps</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap min-w-[120px]">
                                <span class="font-bold text-indigo-400">{{ number_format($town->plots_count) }}</span>
                                <span class="text-xs text-gray-500">({{ $town->sold_plots_count }} sold)</span>
                                <div class="mt-1 h-1 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500" style="width: {{ $soldRate }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                @if ($town->master_plan_map)
                                    <a href="{{ $mediaUrl($town->master_plan_map) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-lg">
                                        🗺️ Plan
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-600">No Map</span>
                                @endif

                                @if ($galleryCount > 0)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-lg">
                                        🖼️ {{ $galleryCount }} Photos
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->total_area ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($town->noc_number)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        ✓ {{ $town->noc_number }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('town_owner.plots', $town->id) }}" wire:navigate class="inline-flex items-center gap-1 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>📍</span> Plots
                                </a>
                                <button type="button" wire:click="editTown({{ $town->id }})" class="inline-flex items-center gap-1 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>✏️</span> Edit
                                </button>
                                <button type="button" wire:click="deleteTown({{ $town->id }})"
                                    wire:confirm="{{ $town->name }} aur is ke {{ $town->plots_count }} plots, map aur gallery hamesha ke liye delete ho jayenge. Kya aap sure hain?"
                                    class="inline-flex items-center gap-1 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 disabled:opacity-40"
                                    @disabled($town->sold_plots_count > 0)
                                    title="{{ $town->sold_plots_count > 0 ? 'Booked/sold plots wali scheme delete nahi ho sakti' : 'Delete scheme' }}">
                                    <span>🗑️</span> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                @if (trim($search) !== '')
                                    <p class="text-base font-semibold">"{{ $search }}" se koi scheme nahi mili.</p>
                                    <button type="button" wire:click="$set('search', '')" class="text-indigo-400 font-bold hover:underline mt-1">Clear search</button>
                                @else
                                    <p class="text-base font-semibold">No schemes added yet.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($towns->hasPages())
            <div class="p-4 border-t border-gray-800/80 bg-gray-950/30">
                {{ $towns->links() }}
            </div>
        @endif
    </div>

</div>
