<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto text-slate-100">

    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-5 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                <i class="fa-solid fa-house-chimney-medical text-amber-500"></i> Add New Property (Admin Portal)
            </h1>
            <p class="text-xs text-slate-400 mt-1">List a property directly or assign it to an Agent / Town Owner.</p>
        </div>
        <a href="{{ route('admin.properties') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 hover:text-amber-400 rounded-xl text-xs font-bold transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3 rounded-xl text-sm flex items-center justify-between">
            <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">

        <!-- Card 1: Core Assignment & Purpose -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800/80 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-shield"></i> Ownership & Listing Type
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Assign To Agent/Owner -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Assign To (Agent / Owner)
                    </label>
                    <select wire:model="agent_id" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">Admin (Self Listed)</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->id }}">
                                {{ $ag->name }} ({{ ucfirst(str_replace('_', ' ', $ag->role)) }}) - {{ $ag->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Purpose *</label>
                    <select wire:model="purpose" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                        <option value="for_sale">For Sale</option>
                        <option value="for_rent">For Rent</option>
                    </select>
                    @error('purpose') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Property Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Property Type *</label>
                    <select wire:model="property_type" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                        <option value="plot">Plot / Land</option>
                        <option value="house">House / Villa</option>
                        <option value="shop">Commercial / Shop</option>
                        <option value="flat">Apartment / Flat</option>
                        <option value="agricultural">Agricultural Land</option>
                    </select>
                    @error('property_type') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Property Specifications & Pricing -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800/80 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-list-check"></i> Property Specs & Pricing
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Title (Optional) -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Listing Title (Optional)</label>
                    <input type="text" wire:model="title" placeholder="e.g. 5 Marla Corner Plot in Prime Location" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                    @error('title') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Area Size -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Area / Size *</label>
                    <input type="text" wire:model="area_size" placeholder="e.g. 5 Marla, 10 Marla, 1 Kanal" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                    @error('area_size') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Total Demand Price -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Total Demand Price (PKR) *</label>
                    <input type="number" wire:model="price" placeholder="e.g. 2500000" class="w-full bg-slate-950 border border-slate-800 text-amber-400 font-bold rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                    @error('price') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Initial Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Initial Status *</label>
                    <select wire:model="status" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                        <option value="available">Available</option>
                        <option value="sold">Sold</option>
                        <option value="rented">Rented</option>
                    </select>
                    @error('status') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Card 3: Location Details & Maps -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800/80 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot"></i> Geographical Location
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- City Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">City *</label>
                    <select wire:model.live="city_id" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">Select City</option>
                        @foreach($cities as $ct)
                            <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Town / Scheme Selection (Optional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Housing Scheme / Society</label>
                    <select wire:model="town_id" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">None / Open City Land</option>
                        @foreach($towns as $tw)
                            <option value="{{ $tw->id }}">{{ $tw->name }}</option>
                        @endforeach
                    </select>
                    @error('town_id') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Address / Location String -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Location / Road / Block *</label>
                    <input type="text" wire:model="location" placeholder="e.g. Block B, Main Boulevard" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                    @error('location') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Google Maps Link -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Google Maps Direction URL</label>
                    <input type="url" wire:model="google_map_url" placeholder="https://maps.google.com/?q=..." class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none">
                    @error('google_map_url') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Card 4: Photos & Description -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800/80 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-images"></i> Media & Description
            </h3>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Detailed Description</label>
                <textarea wire:model="description" rows="4" placeholder="Detail utilities (electricity, water, gas, sewerage) and features..." class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-4 py-3 text-xs sm:text-sm focus:border-amber-500 focus:outline-none"></textarea>
                @error('description') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Property Images (Select Multiple)</label>
                <input type="file" wire:model="images" multiple accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                @error('images.*') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                <!-- Previews -->
                @if ($images)
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mt-4">
                        @foreach ($images as $img)
                            <div class="aspect-square rounded-xl overflow-hidden border border-slate-800 bg-slate-950">
                                <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-4 pt-4">
            <button type="submit" wire:loading.attr="disabled" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold px-8 py-4 rounded-2xl transition shadow-lg shadow-amber-500/25 flex items-center gap-2 text-sm disabled:opacity-50">
                <span wire:loading.remove><i class="fa-solid fa-cloud-arrow-up"></i> Publish Property Now</span>
                <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Saving Listing...</span>
            </button>
        </div>

    </form>
</div>