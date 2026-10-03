<div class="p-4 sm:p-6 lg:p-8 space-y-8 min-h-screen bg-slate-950 text-slate-100">

    <!-- Dashboard Main Heading & Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">System Control Panel</h1>
            <p class="text-xs text-slate-400 mt-1">Platform analytics, user access controls, and monetary parameters.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                System Operational
            </span>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('status'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl text-xs font-semibold shadow-sm">
            <i class="fa-solid fa-circle-check text-sm"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- System Quick Stats (Dark Theme Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Total Regular Users -->
        <div class="bg-[#0a0f1d] rounded-2xl p-6 border border-slate-800/80 shadow-xl relative overflow-hidden group hover:border-indigo-500/50 transition-all duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition-all"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-indigo-400 uppercase tracking-widest">Regular Users</p>
                    <p class="text-3xl font-black text-white mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-4 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-line text-[10px] text-indigo-400"></i> Active Platform Members
            </p>
        </div>

        <!-- Town Scheme Owners -->
        <div class="bg-[#0a0f1d] rounded-2xl p-6 border border-slate-800/80 shadow-xl relative overflow-hidden group hover:border-emerald-500/50 transition-all duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-widest">Housing Scheme Owners</p>
                    <p class="text-3xl font-black text-white mt-2">{{ number_format($totalTownOwners) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-building-user"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-4 flex items-center gap-1.5">
                <i class="fa-solid fa-city text-[10px] text-emerald-400"></i> Managing Projects & Schemes
            </p>
        </div>

        <!-- Property Agents -->
        <div class="bg-[#0a0f1d] rounded-2xl p-6 border border-slate-800/80 shadow-xl relative overflow-hidden group hover:border-amber-500/50 transition-all duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/5 rounded-full blur-xl group-hover:bg-amber-500/10 transition-all"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-amber-400 uppercase tracking-widest">Verified Agents</p>
                    <p class="text-3xl font-black text-white mt-2">{{ number_format($totalAgents) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-4 flex items-center gap-1.5">
                <i class="fa-solid fa-id-badge text-[10px] text-amber-400"></i> Market Service Providers
            </p>
        </div>

    </div>

    <!-- Pending Approvals Livewire Component Wrapper -->
    <div class="bg-[#0a0f1d] rounded-2xl shadow-xl border border-slate-800/80 p-5 sm:p-6">
        <livewire:admin.pending-approvals />
    </div>

    <!-- Fee & Platform Settings Form -->
    <div class="bg-[#0a0f1d] rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-800/80">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-800/80">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Platform Fee & Subscription Charges</h2>
                <p class="text-xs text-slate-400">Manage unlock fees and recurring monthly platform subscriptions.</p>
            </div>
        </div>
        
        @if (session('settings_status'))
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-3.5 rounded-xl text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('settings_status') }}</span>
            </div>
        @endif

        <form wire:submit="updateSettings" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Agent Lead Unlock Fee (PKR)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs font-bold">Rs.</span>
                    <input type="number" step="0.01" wire:model="lead_unlock_fee_agent" 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-12 pr-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-semibold" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Town Owner Lead Unlock Fee (PKR)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs font-bold">Rs.</span>
                    <input type="number" step="0.01" wire:model="lead_unlock_fee_town" 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-12 pr-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-semibold" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Town Owner Monthly Subscription (PKR)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs font-bold">Rs.</span>
                    <input type="number" step="0.01" wire:model="town_owner_monthly_fee" 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-12 pr-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-semibold" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Agent Monthly Subscription (PKR)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs font-bold">Rs.</span>
                    <input type="number" step="0.01" wire:model="agent_monthly_fee" 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-12 pr-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-semibold" />
                </div>
            </div>

            <div class="md:col-span-2 pt-2 flex justify-end">
                <button type="submit" 
                        class="bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black text-xs py-3 px-6 rounded-xl shadow-lg shadow-amber-500/10 hover:shadow-amber-500/20 transition-all duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Fee Configuration</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Manage Approvals (Town Owners & Agents) Table -->
    <div class="bg-[#0a0f1d] rounded-2xl shadow-xl border border-slate-800/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Manage Service Provider Approvals</h3>
                    <p class="text-xs text-slate-400">Review, allow access or restrict housing scheme owners and agents.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] uppercase text-slate-400 border-b border-slate-800/80 font-black tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Provider Details</th>
                        <th class="px-6 py-4">Contact Details</th>
                        <th class="px-6 py-4">Assigned Role</th>
                        <th class="px-6 py-4">Access Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($allProviders as $provider)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 font-bold flex items-center justify-center uppercase shrink-0">
                                        {{ substr($provider->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-white text-sm">{{ $provider->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="block text-slate-200 font-semibold">{{ $provider->email }}</span>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">
                                    <i class="fa-solid fa-phone text-[10px] text-slate-400 mr-1"></i>{{ $provider->phone ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="uppercase text-[10px] font-black px-2.5 py-1 rounded-lg bg-slate-800 text-amber-400 border border-slate-700 tracking-wider">
                                    {{ str_replace('_', ' ', $provider->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($provider->is_approved)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Blocked / Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="toggleApproval({{ $provider->id }})" 
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 shadow-sm border {{ $provider->is_approved ? 'bg-rose-500/10 text-rose-400 border-rose-500/20 hover:bg-rose-600 hover:text-white' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-600 hover:text-white' }}">
                                    {{ $provider->is_approved ? 'Disallow / Block' : 'Approve Access' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 font-medium">
                                <i class="fa-solid fa-users-slash text-2xl mb-2 block text-slate-600"></i>
                                No Town Owners or Agents registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($allProviders->hasPages())
            <div class="p-4 border-t border-slate-800/80 bg-slate-900/40">
                {{ $allProviders->links() }}
            </div>
        @endif
    </div>

</div>