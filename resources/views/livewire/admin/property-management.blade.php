<div class="min-h-screen bg-slate-950 text-slate-100 p-4 sm:p-8 font-sans">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-building text-amber-500"></i> All Listed Properties
            </h1>
            <p class="text-slate-400 text-sm mt-1">Manage, moderate, approve or ban properties posted by sellers and agents</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-center justify-between">
            <span>{{ session('status') }}</span>
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
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6">
                                <span class="text-xs font-mono text-amber-500">#PR-{{ $property->id }}</span>
                                <h3 class="font-bold text-white text-base leading-snug">{{ $property->title }}</h3>
                                <span class="text-xs text-slate-500">{{ $property->type ?? 'Plot/House' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-white">{{ $property->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-400">{{ $property->user->phone ?? 'No Phone' }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-800 text-amber-400">
                                    {{ $property->user->role ?? 'User' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                {{ $property->city->name ?? 'N/A' }}
                                <span class="block text-xs text-slate-500">{{ $property->town->name ?? 'Independent' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-amber-400">PKR {{ number_format($property->price ?? 0) }}</div>
                                <div class="text-xs text-slate-400">{{ $property->area_size ?? 'N/A' }} {{ $property->size_unit ?? 'Marla' }}</div>
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