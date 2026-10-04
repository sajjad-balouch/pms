<div class="min-h-screen bg-slate-950 text-slate-100 p-4 sm:p-8 font-sans">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-building text-amber-500"></i> All Listed Properties
            </h1>
            <p class="text-slate-400 text-sm mt-1">Manage, moderate, edit, approve or ban properties posted by sellers and agents</p>
        </div>
        <div>
            <a href="{{ route('admin.properties.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs sm:text-sm flex items-center gap-2 transition shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-plus"></i> Add Property
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-center justify-between">
            <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 mb-6 shadow-xl backdrop-blur-xl grid grid-cols-1 sm:grid-cols-3 gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Title, ID or Owner Name..." class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-sm" />
        
        <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-sm">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="pending">Pending Approval</option>
            <option value="banned">Banned / Suspended</option>
        </select>
    </div>

    <!-- Properties Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase">
                        <th class="py-4 px-6">Property ID & Title</th>
                        <th class="py-4 px-6">Posted By (Owner/Agent)</th>
                        <th class="py-4 px-6">Location</th>
                        <th class="py-4 px-6">Price & Size</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse($properties as $property) 

                        @php 
                            $cityName = $property->city?->name 
                                ?? $property->city()->first()?->name 
                                ?? $property->town?->city?->name 
                                ?? 'N/A';
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6">
                                <span class="text-xs font-mono text-amber-500">#PR-{{ $property->id }}</span>
                                <h3 class="font-bold text-white text-base leading-snug">{{ $property->title }}</h3>
                                <span class="text-xs text-slate-500 capitalize">{{ $property->property_type ?? $property->type ?? 'Plot/House' }} ({{ str_replace('_', ' ', $property->purpose ?? 'sale') }})</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-white">{{ $property->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-400">{{ $property->user->phone ?? 'No Phone' }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-800 text-amber-400">
                                    {{ $property->user->role ?? 'User' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                {{ $cityName }}
                                <span class="block text-xs text-slate-500">{{ $property->town->name ?? 'Independent' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-amber-400">PKR {{ number_format($property->price ?? 0) }}</div>
                                <div class="text-xs text-slate-400">{{ $property->area_size ?? 'N/A' }} {{ $property->size_unit ?? '' }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($property->is_active === 'active')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                                @elseif($property->is_active === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Banned</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-1">
                                <button wire:click="viewDetails({{ $property->id }})" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white" title="Full Details">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                
                                <button wire:click="openEditModal({{ $property->id }})" class="p-2 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20" title="Edit Property">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                @if($property->is_active !== 'active')
                                    <button wire:click="updateStatus({{ $property->id }}, 'active')" class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20" title="Approve/Active">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @endif

                                @if($property->is_active !== 'banned')
                                    <button wire:click="openBanModal({{ $property->id }})" class="p-2 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20" title="Ban Listing">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-500">No properties found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">{{ $properties->links() }}</div>
    </div>

    <!-- Edit Property Modal -->
    @if($isEditModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md overflow-y-auto">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-4xl p-6 sm:p-8 shadow-2xl my-8 relative">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-amber-400"></i> Edit Property #{{ $editPropertyId }}
                    </h3>
                    <button wire:click="$set('isEditModalOpen', false)" class="text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit="updateProperty" class="space-y-6">
                    
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Title</label>
                            <input type="text" wire:model="edit_title" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                            @error('edit_title') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Purpose</label>
                            <select wire:model="edit_purpose" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                                <option value="for_sale">For Sale</option>
                                <option value="for_rent">For Rent</option>
                            </select>
                            @error('edit_purpose') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Property Type</label>
                            <select wire:model="edit_property_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                                <option value="plot">Plot</option>
                                <option value="house">House</option>
                                <option value="shop">Shop / Commercial</option>
                                <option value="flat">Apartment / Flat</option>
                                <option value="agricultural">Agricultural Land</option>
                            </select>
                            @error('edit_property_type') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Demand Price (PKR)</label>
                            <input type="number" wire:model="edit_price" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-amber-400 font-bold focus:border-amber-500 focus:outline-none">
                            @error('edit_price') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Area / Size</label>
                            <input type="text" wire:model="edit_area_size" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                            @error('edit_area_size') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">City</label>
                            <select wire:model.live="edit_city_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                                <option value="">Select City</option>
                                @foreach($cities as $ct)
                                    <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                                @endforeach
                            </select>
                            @error('edit_city_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Housing Scheme / Town</label>
                            <select wire:model="edit_town_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                                <option value="">None / Open City</option>
                                @foreach($towns as $tw)
                                    <option value="{{ $tw->id }}">{{ $tw->name }}</option>
                                @endforeach
                            </select>
                            @error('edit_town_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Location / Address</label>
                            <input type="text" wire:model="edit_location" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                            @error('edit_location') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Google Maps URL</label>
                            <input type="url" wire:model="edit_google_map_url" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                            @error('edit_google_map_url') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Marketplace Status</label>
                            <select wire:model="edit_status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                                <option value="rented">Rented</option>
                            </select>
                            @error('edit_status') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Admin Approval Status</label>
                            <select wire:model="edit_is_active" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="banned">Banned</option>
                            </select>
                            @error('edit_is_active') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Description</label>
                            <textarea wire:model="edit_description" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none"></textarea>
                            @error('edit_description') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Images Section (Existing & New Uploads) -->
                    <div class="border-t border-slate-800 pt-5 space-y-4">
                        <label class="block text-xs font-bold text-amber-400 uppercase tracking-wider">
                            <i class="fa-solid fa-images mr-1"></i> Manage Images
                        </label>

                        <!-- Existing Images -->
                        @if(count($existing_images) > 0)
                            <div>
                                <span class="text-xs text-slate-400 block mb-2">Current Images (Click trash icon to remove):</span>
                                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                                    @foreach($existing_images as $index => $imgPath)
                                        <div class="relative group rounded-xl overflow-hidden border border-slate-800 bg-slate-950 aspect-square">
                                            <img src="{{ asset('public/' . $imgPath) }}" class="w-full h-full object-cover">
                                            <button type="button" wire:click="removeExistingImage({{ $index }})" class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-500 text-white rounded-lg p-1.5 text-xs opacity-90 hover:opacity-100 shadow-md transition" title="Delete Image">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Upload New Images -->
                        <div>
                            <span class="text-xs text-slate-400 block mb-2">Upload More Images:</span>
                            <input type="file" wire:model="new_images" multiple accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer bg-slate-950 p-2 border border-slate-800 rounded-xl">
                            @error('new_images.*') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                            <!-- Temporary Previews of newly selected images -->
                            @if ($new_images)
                                <div class="mt-3">
                                    <span class="text-[11px] text-amber-400/80 block mb-1">New Selected Previews:</span>
                                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                                        @foreach ($new_images as $newImg)
                                            <div class="aspect-square rounded-xl overflow-hidden border border-amber-500/40 bg-slate-950">
                                                <img src="{{ $newImg->temporaryUrl() }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('isEditModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="updateProperty">Save Changes</span>
                            <span wire:loading wire:target="updateProperty"><i class="fa-solid fa-spinner fa-spin"></i> Saving...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    <!-- Details Modal -->
    @if($isDetailModalOpen && $selectedProperty)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                    <h3 class="text-xl font-bold text-white">Property Inspector #{{ $selectedProperty->id }}</h3>
                    <button wire:click="$set('isDetailModalOpen', false)" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="space-y-4 text-sm text-slate-300">
                    <div class="grid grid-cols-2 gap-4 bg-slate-950 p-4 rounded-xl border border-slate-800">
                        <div><strong class="text-slate-400 block text-xs">Owner Name:</strong> {{ $selectedProperty->user->name ?? 'N/A' }}</div>
                        <div><strong class="text-slate-400 block text-xs">Owner Contact:</strong> {{ $selectedProperty->user->phone ?? 'N/A' }}</div>
                        <div><strong class="text-slate-400 block text-xs">Email:</strong> {{ $selectedProperty->user->email ?? 'N/A' }}</div>
                        <div><strong class="text-slate-400 block text-xs">Role:</strong> {{ strtoupper($selectedProperty->user->role ?? 'USER') }}</div>
                    </div>
                    <div>
                        <strong class="text-slate-400 block text-xs mb-1">Description:</strong>
                        <p class="bg-slate-950 p-3 rounded-xl border border-slate-800">{{ $selectedProperty->description ?? 'No description provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Ban Modal -->
    @if($isBanModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-2">Ban / Suspend Property</h3>
                <p class="text-xs text-slate-400 mb-4">Provide a reason for banning this property listing.</p>
                
                <textarea wire:model="banReason" placeholder="Enter reason (e.g., Fake listing, Invalid documents)..." class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl text-white text-sm focus:outline-none focus:border-amber-500 mb-4"></textarea>
                @error('banReason') <p class="text-red-400 text-xs mb-3">{{ $message }}</p> @enderror

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('isBanModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold">Cancel</button>
                    <button wire:click="banProperty" class="px-4 py-2 rounded-xl bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-500/20">Confirm Ban</button>
                </div>
            </div>
        </div>
    @endif
</div>