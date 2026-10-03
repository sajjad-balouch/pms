<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Housing Schemes Management</h1>
                <p class="text-slate-400 text-xs mt-1">Add, edit, or delete registered housing schemes and towns.</p>
            </div>
            
            <button type="button" wire:click="openCreateModal" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-plus"></i> Add Housing Scheme
            </button>
        </div>

        <!-- Alerts -->
        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter / Search -->
        <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 flex items-center justify-between">
            <div class="w-full sm:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by scheme name, city or location..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-amber-400">
            </div>
        </div>

        <!-- Towns Table -->
        <div class="bg-slate-800/40 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-700/50">
                        <tr>
                            <th class="p-4">Scheme / Town Name</th>
                            <th class="p-4">City</th>
                            <th class="p-4">Location</th>
                            <th class="p-4">Total Plots</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($towns as $town)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-4">
                                    <div class="font-bold text-white text-sm">{{ $town->name }}</div>
                                    @if($town->description)
                                        <div class="text-slate-400 text-[11px] truncate max-w-xs">{{ $town->description }}</div>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-300 font-semibold">
                                    {{ $town->city }}
                                </td>
                                <td class="p-4 text-slate-400">
                                    {{ $town->location ?? 'N/A' }}
                                </td>
                                <td class="p-4 font-bold text-amber-400">
                                    {{ $town->total_plots ?? 0 }}
                                </td>
                                <td class="p-4">
                                    @if($town->is_active)
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold text-[10px]">Active</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-slate-700/50 text-slate-400 border border-slate-600/30 font-bold text-[10px]">Inactive</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="openEditModal({{ $town->id }})" class="bg-amber-500/10 hover:bg-amber-500 text-amber-400 hover:text-slate-950 font-bold px-3 py-1.5 rounded-lg text-[11px] transition">
                                            Edit
                                        </button>
                                        <button wire:click="deleteTown({{ $town->id }})" wire:confirm="Are you sure you want to delete this housing scheme?" class="bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white font-bold px-3 py-1.5 rounded-lg text-[11px] transition">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">No housing schemes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-800">
                {{ $towns->links() }}
            </div>
        </div>

    </div>

    <!-- Create / Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 p-6 sm:p-8 rounded-3xl max-w-lg w-full space-y-5 relative shadow-2xl">
                
                <button type="button" wire:click="$set('showModal', false)" class="absolute top-5 right-5 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div>
                    <h3 class="text-xl font-extrabold text-white">
                        {{ $isEditMode ? 'Edit Housing Scheme' : 'Add New Housing Scheme' }}
                    </h3>
                    <p class="text-slate-400 text-xs mt-1">Specify town details and geographic position.</p>
                </div>

                <form wire:submit.prevent="saveTown" class="space-y-4">
                    
                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1">Scheme / Town Name</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="e.g. Etihad Town Phase 1">
                        @error('name') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-400 font-bold block mb-1">City</label>
                            <input type="text" wire:model="city" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="e.g. Lahore / Rahim Yar Khan">
                            @error('city') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-bold block mb-1">Total Plots</label>
                            <input type="number" wire:model="total_plots" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="0">
                            @error('total_plots') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1">Location / Main Address</label>
                        <input type="text" wire:model="location" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="e.g. Main Raiwind Road">
                        @error('location') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1">Description (Optional)</label>
                        <textarea wire:model="description" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="Short description about features or phase details..."></textarea>
                        @error('description') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="text-amber-500 focus:ring-amber-400 rounded">
                            <span class="text-xs font-bold text-white">Active Status</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-5 py-2.5 rounded-xl text-xs transition">
                            Cancel
                        </button>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold px-5 py-2.5 rounded-xl text-xs transition shadow-lg shadow-amber-500/20">
                            {{ $isEditMode ? 'Update Scheme' : 'Save Scheme' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>