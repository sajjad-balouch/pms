<div class="min-h-screen bg-slate-950 text-slate-100 p-4 sm:p-8 font-sans selection:bg-amber-500 selection:text-slate-950">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-city text-amber-500"></i>
                City Management
            </h1>
            <p class="text-slate-400 text-sm mt-1">Manage all geographical locations and operational areas across Pakistan</p>
        </div>
        <button 
            wire:click="createCity" 
            class="px-5 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-amber-500/20 active:scale-95 transition-all flex items-center justify-center gap-2"
        >
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add New City</span>
        </button>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 sm:p-6 mb-6 shadow-xl backdrop-blur-xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Search Input -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search city by name..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm transition-all"
                />
            </div>

            <!-- Province Filter -->
            <div>
                <select 
                    wire:model.live="provinceFilter" 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm transition-all"
                >
                    <option value="">All Provinces / Regions</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select 
                    wire:model.live="statusFilter" 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm transition-all"
                >
                    <option value="">All Statuses</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive Only</option>
                </select>
            </div>

            <!-- Items Per Page -->
            <div>
                <select 
                    wire:model.live="perPage" 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm transition-all"
                >
                    <option value="10">Show 10 per page</option>
                    <option value="25">Show 25 per page</option>
                    <option value="50">Show 50 per page</option>
                    <option value="100">Show 100 per page</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-4 px-6">Order</th>
                        <th class="py-4 px-6">City Name</th>
                        <th class="py-4 px-6">Province / Region</th>
                        <th class="py-4 px-6 text-center">Featured</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse ($cities as $city)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 text-slate-500 font-mono text-xs">#{{ $city->sort_order }}</td>
                            <td class="py-4 px-6 font-semibold text-white">
                                {{ $city->name }}
                                <span class="block text-xs font-normal text-slate-500 font-mono">{{ $city->slug }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-xs border border-slate-700/50">
                                    <i class="fa-solid fa-map-pin text-[10px] text-amber-500"></i>
                                    {{ $city->province }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button 
                                    wire:click="toggleFeatured({{ $city->id }})" 
                                    class="p-1.5 rounded-lg transition-colors {{ $city->is_featured ? 'text-amber-400 hover:text-amber-300 bg-amber-500/10' : 'text-slate-600 hover:text-slate-400 bg-slate-800/50' }}"
                                    title="Toggle Featured"
                                >
                                    <i class="fa-solid fa-star text-base"></i>
                                </button>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button 
                                    wire:click="toggleStatus({{ $city->id }})" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-all {{ $city->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full {{ $city->is_active ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                    {{ $city->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button 
                                    wire:click="editCity({{ $city->id }})" 
                                    class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors"
                                    title="Edit City"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $city->id }})" 
                                    class="p-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-colors"
                                    title="Delete City"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-city text-3xl mb-3 text-slate-700 block"></i>
                                No cities found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-800">
            {{ $cities->links() }}
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative">
                
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
                    <h3 class="text-xl font-bold text-white">
                        {{ $cityIdBeingEdited ? 'Edit City' : 'Add New City' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-500 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit="saveCity" class="space-y-4">
                    
                    <!-- City Name -->
                    <div>
                        <label for="city-name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">City Name</label>
                        <input 
                            type="text" 
                            id="city-name"
                            wire:model="name" 
                            placeholder="e.g. Faisalabad" 
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 text-sm transition-all"
                            required
                        />
                        @error('name') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Province -->
                    <div>
                        <label for="city-province" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Province / Region</label>
                        <select 
                            id="city-province"
                            wire:model="province" 
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-500 text-sm transition-all"
                            required
                        >
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                        @error('province') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="city-order" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Sort Priority Order</label>
                        <input 
                            type="number" 
                            id="city-order"
                            wire:model="sort_order" 
                            placeholder="0" 
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 text-sm transition-all"
                            min="0"
                            required
                        />
                        @error('sort_order') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Switches Grid -->
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <label class="flex items-center gap-3 p-3 bg-slate-950 border border-slate-800 rounded-xl cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700">
                            <span class="text-xs font-semibold text-slate-300">Active Status</span>
                        </label>

                        <label class="flex items-center gap-3 p-3 bg-slate-950 border border-slate-800 rounded-xl cursor-pointer">
                            <input type="checkbox" wire:model="is_featured" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700">
                            <span class="text-xs font-semibold text-slate-300">Featured City</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-800">
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="px-5 py-2.5 rounded-xl border border-slate-800 text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-semibold transition-all"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-amber-500/20 active:scale-95 transition-all flex items-center gap-2 disabled:opacity-70"
                        >
                            <span wire:loading.remove wire:target="saveCity">Save City</span>
                            <span wire:loading wire:target="saveCity" class="flex items-center gap-2">
                                <i class="fa-solid fa-spinner animate-spin"></i> Saving...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($isDeleteModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl text-center">
                <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center mx-auto mb-4 text-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Delete City?</h3>
                <p class="text-sm text-slate-400 mb-6">Are you sure you want to delete this city? This action cannot be undone if properties are linked to it.</p>
                
                <div class="flex items-center justify-center gap-3">
                    <button 
                        wire:click="$set('isDeleteModalOpen', false)" 
                        class="px-5 py-2.5 rounded-xl border border-slate-800 text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-semibold transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="deleteCity" 
                        class="px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm shadow-lg shadow-rose-500/20 transition-all"
                    >
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>