<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Flash Alert -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-semibold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Direct Property Listings</h2>
            <p class="text-sm text-slate-400">مکان، دکان، پلاٹ، اپارٹمنٹ یا زرعی زمین براہِ راست لسٹ کریں</p>
        </div>

        <button wire:click="create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Property
        </button>
    </div>

    <!-- Filters Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-800/90 p-4 rounded-2xl shadow-md border border-slate-700/80">
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search location, title..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-white placeholder-slate-500">
            <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
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
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse ($properties as $property)
                        <tr class="hover:bg-slate-700/30 transition-colors">
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
                                <div class="text-xs text-slate-400">{{ $property->location }}, {{ $property->city }}</div>
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
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $property->id }})" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Edit</button>
                                <button wire:click="delete({{ $property->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="text-xs font-semibold text-rose-400 hover:text-rose-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No individual properties listed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700/80">
            {{ $properties->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl border border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">
                    {{ $property_id ? 'Edit Property Details' : 'Add Property Information' }}
                </h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Property Title / Description Name *</label>
                    <input type="text" wire:model="title" placeholder="e.g. 10 Marla Luxury House for Sale" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('title') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Property Type *</label>
                        <select wire:model="property_type" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="house">House</option>
                            <option value="apartment">Apartment</option>
                            <option value="shop">Shop / Commercial</option>
                            <option value="agricultural">Agricultural Land</option>
                            <option value="plot">Plot / Land</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Purpose *</label>
                        <select wire:model="purpose" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="for_sale">For Sale</option>
                            <option value="for_rent">For Rent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Town / Scheme (Optional)</label>
                        <select wire:model="town_id" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">None (Individual)</option>
                            @foreach($towns as $town)
                                <option value="{{ $town->id }}">{{ $town->name }}</option>
                            @endforeach
                        </select>
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
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">City *</label>
                        <input type="text" wire:model="city" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Location / Address *</label>
                        <input type="text" wire:model="location" placeholder="e.g. Main Canal Road, Near Mall" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('location') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <!-- Google Map Link Field (Optional) -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Google Map Location Link (Optional)</label>
                        <div class="relative">
                            <input type="url" wire:model="google_map_url" placeholder="https://maps.app.goo.gl/..." class="w-full pl-9 rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Google Maps se "Share" link copy karke yahan paste karein.</span>
                        @error('google_map_url') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Upload Images</label>
                    <input type="file" wire:model="new_images" multiple class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Description & Amenities</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500 editor" placeholder="Mention gas, water, electricity, facing road, etc."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-xl font-semibold text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-xs shadow-md">Save Property</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>