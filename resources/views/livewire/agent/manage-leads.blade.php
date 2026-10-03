<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Flash Alert -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-semibold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Leads Management</h2>
            <p class="text-sm text-slate-400">تمام کلائنٹس اور ان کی انکوائریز کو میپ کریں</p>
        </div>

        <button wire:click="create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Lead
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-col md:flex-row gap-4 bg-slate-800/90 p-4 rounded-2xl shadow-md border border-slate-700/80">
        <div class="flex-1 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or phone..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder-slate-500">
            <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <div>
            <select wire:model.live="statusFilter" class="w-full md:w-auto px-4 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-white">
                <option value="">All Statuses</option>
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="site_visit">Site Visit</option>
                <option value="negotiation">Negotiation</option>
                <option value="closed_won">Closed Won</option>
                <option value="closed_lost">Closed Lost</option>
            </select>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-6 py-4">Client Name</th>
                        <th class="px-6 py-4">Phone / Email</th>
                        <th class="px-6 py-4">Interested Town / Plot</th>
                        <th class="px-6 py-4">Budget</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $lead->client_name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-200">{{ $lead->client_phone }}</div>
                                <div class="text-xs text-slate-400">{{ $lead->client_email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-300">{{ $lead->town?->name ?? 'General' }}</div>
                                @if($lead->plot)
                                    <div class="text-xs font-bold text-indigo-400">Plot # {{ $lead->plot->plot_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-400">
                                {{ $lead->budget ? 'Rs. ' . number_format($lead->budget) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold capitalize
                                    {{ $lead->status === 'closed_won' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $lead->status === 'new' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                    {{ $lead->status === 'negotiation' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ in_array($lead->status, ['contacted', 'site_visit']) ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                                    {{ $lead->status === 'closed_lost' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}">
                                    {{ str_replace('_', ' ', $lead->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $lead->id }})" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Edit</button>
                                <button wire:click="delete({{ $lead->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="text-xs font-semibold text-rose-400 hover:text-rose-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700/80">
            {{ $leads->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">
                    {{ $lead_id ? 'Edit Lead' : 'Add New Lead' }}
                </h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form wire:submit.prevent="store" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Client Name *</label>
                    <input type="text" wire:model="client_name" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('client_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Phone *</label>
                        <input type="text" wire:model="client_phone" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('client_phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Email</label>
                        <input type="email" wire:model="client_email" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Town / Scheme</label>
                        <select wire:model.live="town_id" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Scheme</option>
                            @foreach($towns as $town)
                                <option value="{{ $town->id }}">{{ $town->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Plot Number</label>
                        <select wire:model="plot_id" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Plot</option>
                            @foreach($availablePlots as $plot)
                                <option value="{{ $plot->id }}">Plot # {{ $plot->plot_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="site_visit">Site Visit</option>
                            <option value="negotiation">Negotiation</option>
                            <option value="closed_won">Closed Won</option>
                            <option value="closed_lost">Closed Lost</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Budget (PKR)</label>
                        <input type="number" wire:model="budget" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Notes / Requirement</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-xl font-semibold text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-xs shadow-md">Save Lead</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>