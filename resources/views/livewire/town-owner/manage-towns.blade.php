<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Header & Back Link -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>🏘️</span> Manage Housing Schemes & Towns
            </h2>
            <p class="text-xs text-gray-400 mt-1">Apni tamam housing schemes ko yahan se add aur manage karein.</p>
        </div>
        <a href="{{ route('town_owner.dashboard') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">
            <span>←</span> Back to Dashboard
        </a>
    </div>

    <!-- Success Flash Message -->
    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Add / Edit Town Form Card -->
    <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-6">
        <div class="border-b border-gray-800/80 pb-4 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>{{ $editingTownId ? '✏️' : '✨' }}</span> 
                    {{ $editingTownId ? 'Edit Housing Scheme' : 'Add New Project / Housing Scheme' }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $editingTownId ? 'Town ki tafseelat tabdeel karke update karein.' : 'Nayi housing scheme register karne ke liye niche details darj karein.' }}
                </p>
            </div>
            @if($editingTownId)
                <button type="button" wire:click="cancelEdit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl transition-colors">
                    ✕ Cancel Edit
                </button>
            @endif
        </div>
        
        <form wire:submit.prevent="createTown" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Town Name *</label>
                <input type="text" wire:model="name" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. Green Valley City" required />
                @error('name') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">City *</label>
                <input type="text" wire:model="city" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. Lahore, Multan" required />
                @error('city') <span class="text-rose-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

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

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Area</label>
                <input type="text" wire:model="total_area" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. 150 Kanal" />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">NOC Number / Approval</label>
                <input type="text" wire:model="noc_number" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. LDA/NOC/2026-10" />
            </div>

            <div class="md:col-span-2 pt-2 flex items-center gap-3">
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 {{ $editingTownId ? 'bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border-amber-500/30' : 'bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border-indigo-500/30' }} border font-bold py-2.5 px-6 rounded-xl text-sm shadow-sm transition-all duration-200">
                    <span wire:loading.remove>{{ $editingTownId ? 'Update Housing Scheme' : 'Save Housing Scheme' }}</span>
                    <span wire:loading class="animate-pulse">Saving...</span>
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
                        <th scope="col" class="px-6 py-4 text-left font-bold">Map Link</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Area</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">NOC Number</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($towns as $town)
                        @php
                            $mapUrl = !empty($town->google_map_url) 
                                ? $town->google_map_url 
                                : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode(($town->location ?? '') . ', ' . ($town->city ?? ''));
                        @endphp
                        <tr class="hover:bg-gray-800/40 transition-colors {{ $editingTownId === $town->id ? 'bg-amber-500/10 border-l-4 border-l-amber-500' : '' }}">
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">{{ $town->name }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->city }} - {{ $town->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-emerald-400 hover:text-emerald-300 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg transition-colors">
                                    <span>📍</span> View Map
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->total_area ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ $town->noc_number ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('town_owner.plots', $town->id) }}" class="inline-flex items-center gap-1 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>📍</span> Manage Plots
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