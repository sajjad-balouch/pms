<div class="p-6 space-y-6" x-data="{ activeTab: 'overview' }">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white">{{ __('User Management') }}</h1>
            <p class="text-xs text-slate-400 mt-1">{{ __('View complete activity, properties, schemes and plots of every user.') }}</p>
        </div>

        <button wire:click="openCreateModal" 
                class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-amber-500/20 transition">
            <i class="fa-solid fa-user-plus"></i>
            <span>{{ __('Add New User') }}</span>
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session('status'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl text-xs font-bold">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl text-xs font-bold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search, filters & export -->
    <div class="bg-slate-900 rounded-2xl p-4 border border-slate-800 space-y-3">
        <div class="flex flex-wrap gap-2 text-[11px] font-bold">
            @foreach (['' => __('All'), 'user' => __('Buyers'), 'agent' => __('Agents'), 'town_owner' => __('Town Owners'), 'admin' => __('Admins')] as $key => $text)
                <button type="button" wire:click="$set('roleFilter', '{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg border {{ $roleFilter === $key ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-white' }}">
                    {{ $text }}
                    <span class="opacity-70">{{ $key === '' ? $roleCounts->sum() : ($roleCounts[$key] ?? 0) }}</span>
                </button>
            @endforeach
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name, email or phone...') }}"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500 transition" />
            </div>
            <select wire:model.live="statusFilter" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <option value="">{{ __('All statuses') }}</option>
                <option value="approved">{{ __('Active') }}</option>
                <option value="blocked">{{ __('Blocked / Pending') }}</option>
            </select>
            <button type="button" wire:click="exportCsv" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-600/30">
                <i class="fa-solid fa-file-csv mr-1"></i> {{ __('Export CSV') }}
            </button>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-slate-900 rounded-2xl shadow-xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase text-slate-400 border-b border-slate-800 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">{{ __('User Details') }}</th>
                        <th class="px-6 py-4">{{ __('Phone') }}</th>
                        <th class="px-6 py-4">{{ __('Role') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <button wire:click="viewUserActivity({{ $user->id }})" class="flex items-center gap-3 text-left group">
                                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 font-black flex items-center justify-center uppercase shrink-0 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-white block text-xs group-hover:text-amber-400 transition">
                                            {{ $user->name }} 
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition"></i>
                                        </span>
                                        <span class="text-[11px] text-slate-400 block">{{ $user->email }}</span>
                                    </div>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-300">
                                {{ $user->phone ?? 'N/A' }}
                                <span class="block text-[11px] text-slate-500 mt-0.5">{{ __('Wallet') }}: Rs. {{ number_format((float) $user->wallet_balance) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleBadge = match($user->role) {
                                        'admin' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'town_owner' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'agent' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                                    };
                                @endphp
                                <span class="uppercase text-[10px] font-extrabold px-2.5 py-1 rounded-lg border {{ $roleBadge }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->is_approved)
                                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ __('Blocked') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="viewUserActivity({{ $user->id }})" 
                                            title="{{ __('View Complete Activity') }}"
                                            class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 transition border border-slate-700">
                                        <i class="fa-solid fa-chart-line text-sky-400"></i>
                                    </button>

                                    <button wire:click="openWalletModal({{ $user->id }})"
                                            title="{{ __('Adjust Wallet (credit / debit)') }}"
                                            class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 transition border border-slate-700">
                                        <i class="fa-solid fa-wallet text-emerald-400"></i>
                                    </button>

                                    <button wire:click="openPasswordModal({{ $user->id }})"
                                            title="{{ __('Change Password') }}"
                                            class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 transition border border-slate-700">
                                        <i class="fa-solid fa-key text-amber-400"></i>
                                    </button>

                                    @if ($user->id !== auth()->id())
                                        <button wire:click="toggleBlock({{ $user->id }})" 
                                                title="{{ $user->is_approved ? 'Block User' : 'Unblock User' }}"
                                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $user->is_approved ? 'bg-amber-500/10 text-amber-400 border-amber-500/20 hover:bg-amber-600 hover:text-slate-950' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-600 hover:text-white' }}">
                                            <i class="fa-solid {{ $user->is_approved ? 'fa-user-lock' : 'fa-user-check' }}"></i>
                                        </button>

                                        <!-- Delete User Button with Confirmation -->
                                        <button wire:click="deleteUser({{ $user->id }})" 
                                                wire:confirm="{{ __('Delete this user permanently? Accounts with wallet balance, payments, customers or sold plots cannot be deleted — block them instead.') }}"
                                                title="{{ __('Delete User Permanently') }}"
                                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition border bg-rose-500/10 text-rose-400 border-rose-500/20 hover:bg-rose-600 hover:text-white">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs font-medium">{{ __('No users found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: USER ACTIVITY & SCHEMES DASHBOARD -->
    @if ($showActivityModal && $selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-800 max-w-5xl w-full max-h-[90vh] overflow-y-auto space-y-6">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-800 pb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 font-black text-xl flex items-center justify-center uppercase shrink-0">
                            {{ substr($selectedUser->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-white flex items-center gap-2">
                                {{ $selectedUser->name }}
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md border bg-amber-500/10 text-amber-400 border-amber-500/20">
                                    {{ $selectedUser->role }}
                                </span>
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5"><i class="fa-solid fa-envelope mr-1"></i> {{ $selectedUser->email }} &nbsp;|&nbsp; <i class="fa-solid fa-phone mr-1"></i> {{ $selectedUser->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <button wire:click="$set('showActivityModal', false)" class="text-slate-400 hover:text-white p-2 rounded-xl bg-slate-800/60">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Tabs -->
                <div class="flex items-center gap-2 border-b border-slate-800 pb-3 overflow-x-auto">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition">
                        <i class="fa-solid fa-chart-pie mr-1.5"></i> {{ __('Overview') }}
                    </button>

                    <button @click="activeTab = 'towns'" 
                            :class="activeTab === 'towns' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot mr-1"></i> Town Schemes & Plots
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40">{{ count($userTowns) }}</span>
                    </button>

                    <button @click="activeTab = 'properties'" 
                            :class="activeTab === 'properties' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-building mr-1"></i> {{ __('Listed Properties') }} 
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40">{{ count($userProperties) }}</span>
                    </button>

                    <button @click="activeTab = 'unlocks'" 
                            :class="activeTab === 'unlocks' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-lock-open mr-1"></i> {{ __('Unlocks') }}
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40">{{ count($userUnlockedProperties) }}</span>
                    </button>

                    <button @click="activeTab = 'wallet'" 
                            :class="activeTab === 'wallet' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-wallet mr-1"></i> {{ __('Wallet') }}
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40">{{ count($userWalletTransactions) }}</span>
                    </button>
                </div>

                <!-- TAB 1: OVERVIEW -->
                <div x-show="activeTab === 'overview'" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                            <span class="text-slate-400 text-xs font-bold block">{{ __('Town Schemes Managed') }}</span>
                            <span class="text-2xl font-black text-sky-400 mt-1 block">{{ count($userTowns) }}</span>
                        </div>
                        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                            <span class="text-slate-400 text-xs font-bold block">{{ __('Total Schemes Plots') }}</span>
                            <span class="text-2xl font-black text-indigo-400 mt-1 block">
                                {{ $userTowns->sum('total_plots_count') }}
                            </span>
                        </div>
                        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                            <span class="text-slate-400 text-xs font-bold block">{{ __('Properties Listed') }}</span>
                            <span class="text-2xl font-black text-amber-400 mt-1 block">{{ count($userProperties) }}</span>
                        </div>
                        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                            <span class="text-slate-400 text-xs font-bold block">{{ __('Account Status') }}</span>
                            <span class="text-lg font-black {{ $selectedUser->is_approved ? 'text-emerald-400' : 'text-rose-400' }} mt-1 block">
                                {{ $selectedUser->is_approved ? 'Active' : 'Blocked' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: MANAGED TOWNS & DETAILED PLOTS BREAKDOWN -->
                <div x-show="activeTab === 'towns'" class="space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Housing schemes managed by this user and plot statuses') }}</h4>
                    
                    @forelse($userTowns as $town)
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-800/80">
                                <div>
                                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                                        <i class="fa-solid fa-city text-sky-400"></i>
                                        {{ $town->name }}
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-semibold">
                                            NOC: {{ $town->noc_number ?? 'N/A' }}
                                        </span>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-1">
                                        <i class="fa-solid fa-location-dot text-rose-400 mr-1"></i> {{ $town->location }}, {{ $town->city }} &bull; Total Area: {{ $town->total_area ?? 'N/A' }}
                                    </p>
                                </div>
                                <button wire:click="viewTownPlots({{ $town->id }})" 
                                        class="px-3.5 py-2 rounded-xl bg-sky-500/10 hover:bg-sky-500 text-sky-400 hover:text-slate-950 text-xs font-black border border-sky-500/20 transition self-start sm:self-auto">
                                    <i class="fa-solid fa-list-check mr-1"></i> View All Plots List ({{ $town->total_plots_count }})
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                <div class="bg-slate-900 p-3 rounded-xl border border-slate-800">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ __('Total Plots') }}</span>
                                    <span class="text-base font-black text-white mt-0.5 block">{{ $town->total_plots_count }}</span>
                                </div>
                                <div class="bg-emerald-500/10 p-3 rounded-xl border border-emerald-500/20">
                                    <span class="text-[10px] text-emerald-400 uppercase font-bold block">{{ __('Available') }}</span>
                                    <span class="text-base font-black text-emerald-400 mt-0.5 block">{{ $town->available_plots_count }}</span>
                                </div>
                                <div class="bg-amber-500/10 p-3 rounded-xl border border-amber-500/20">
                                    <span class="text-[10px] text-amber-400 uppercase font-bold block">{{ __('Booked') }}</span>
                                    <span class="text-base font-black text-amber-400 mt-0.5 block">{{ $town->booked_plots_count }}</span>
                                </div>
                                <div class="bg-rose-500/10 p-3 rounded-xl border border-rose-500/20">
                                    <span class="text-[10px] text-rose-400 uppercase font-bold block">{{ __('Sold') }}</span>
                                    <span class="text-base font-black text-rose-400 mt-0.5 block">{{ $town->sold_plots_count }}</span>
                                </div>
                                <div class="bg-indigo-500/10 p-3 rounded-xl border border-indigo-500/20 col-span-2 sm:col-span-1">
                                    <span class="text-[10px] text-indigo-400 uppercase font-bold block">{{ __('Types') }}</span>
                                    <span class="text-xs font-bold text-slate-200 mt-0.5 block">
                                        {{ __('Res') }}: <strong class="text-white">{{ $town->residential_plots_count }}</strong> | {{ __('Com') }}: <strong class="text-white">{{ $town->commercial_plots_count }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-6 text-center">{{ __('No housing scheme is registered under this user.') }}</p>
                    @endforelse
                </div>

                <!-- TAB 3: LISTED PROPERTIES -->
                <div x-show="activeTab === 'properties'" class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Properties listed by this agent') }}</h4>
                    @forelse($userProperties as $prop)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950/60 border border-slate-800">
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $prop->title }}</span>
                                <span class="text-[11px] text-slate-400">{{ $prop->location }}, {{ $prop->city }} &bull; {{ $prop->area_size }} &bull; PKR {{ number_format($prop->price) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase font-extrabold px-2 py-1 rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    {{ str_replace('_', ' ', $prop->purpose) }}
                                </span>
                                <span class="text-[10px] uppercase font-extrabold px-2 py-1 rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $prop->property_type }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-4 text-center">{{ __('This user has not listed any property yet.') }}</p>
                    @endforelse
                </div>

                <!-- TAB 4: PROPERTY UNLOCKS -->
                <div x-show="activeTab === 'unlocks'" class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Properties unlocked by this user') }}</h4>
                    @forelse($userUnlockedProperties as $unlocked)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950/60 border border-slate-800">
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $unlocked->title }}</span>
                                <span class="text-[11px] text-slate-400">{{ $unlocked->city }} &bull; PKR {{ number_format($unlocked->price) }}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-semibold">
                                {{ \Carbon\Carbon::parse($unlocked->created_at)->format('d M, Y h:i A') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-4 text-center">{{ __('This user has not unlocked any property yet.') }}</p>
                    @endforelse
                </div>

                <!-- TAB 5: WALLET TRANSACTIONS -->
                <div x-show="activeTab === 'wallet'" class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Wallet History') }}</h4>
                    @forelse($userWalletTransactions as $tx)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950/60 border border-slate-800">
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $tx->description ?? 'Wallet Transaction' }}</span>
                                <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($tx->created_at)->format('d M, Y h:i A') }}</span>
                            </div>
                            <span class="text-xs font-black {{ str_contains(strtolower($tx->type ?? ''), 'credit') ? 'text-emerald-400' : 'text-amber-400' }}">
                                PKR {{ number_format($tx->amount ?? 0) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-4 text-center">{{ __('No wallet transactions found.') }}</p>
                    @endforelse
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-800">
                    <button wire:click="$set('showActivityModal', false)" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700">
                        {{ __('Close') }}
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL: DETAILED TOWN PLOTS MODAL -->
    @if ($showPlotsModal)
        @teleport('body')
            <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
                <div class="bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-800 max-w-4xl w-full max-h-[85vh] overflow-y-auto space-y-4 relative z-[100000]">
                    
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div>
                            <h3 class="text-lg font-black text-white flex items-center gap-2">
                                <i class="fa-solid fa-map-pin text-amber-400"></i>
                                {{ $selectedTownName }} - Plots Inventory
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('All plots of this scheme with status and installment plans.') }}</p>
                        </div>
                        <button wire:click="$set('showPlotsModal', false)" class="text-slate-400 hover:text-white p-2 rounded-xl bg-slate-800">
                            <i class="fa-solid fa-xmark text-base"></i>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950 uppercase text-slate-400 font-bold border-b border-slate-800">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Plot #') }}</th>
                                    <th class="px-4 py-3">{{ __('Block') }}</th>
                                    <th class="px-4 py-3">{{ __('Type') }}</th>
                                    <th class="px-4 py-3">{{ __('Size') }}</th>
                                    <th class="px-4 py-3">{{ __('Total Price') }}</th>
                                    <th class="px-4 py-3">{{ __('Advance') }}</th>
                                    <th class="px-4 py-3">{{ __('Installments') }}</th>
                                    <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                @forelse($selectedTownPlots as $plot)
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="px-4 py-3 font-extrabold text-white">#{{ $plot->plot_number }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $plot->block_name ?? 'Main' }}</td>
                                        <td class="px-4 py-3 uppercase font-semibold text-slate-400">{{ $plot->type }}</td>
                                        <td class="px-4 py-3 font-semibold text-amber-400">{{ $plot->size }}</td>
                                        <td class="px-4 py-3 font-bold text-white">PKR {{ number_format($plot->total_price) }}</td>
                                        <td class="px-4 py-3 text-slate-300">PKR {{ number_format($plot->down_payment) }}</td>
                                        <td class="px-4 py-3 text-slate-400">{{ $plot->total_installments }} Months</td>
                                        <td class="px-4 py-3 text-right">
                                            @php
                                                $statusBadge = match($plot->status) {
                                                    'available' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                    'booked' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                                    'sold' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                                    default => 'bg-slate-800 text-slate-300',
                                                };
                                            @endphp
                                            <span class="uppercase text-[9px] font-extrabold px-2 py-0.5 rounded border {{ $statusBadge }}">
                                                {{ $plot->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-slate-500">{{ __('No plots have been added to this scheme yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end pt-3 border-t border-slate-800">
                        <button wire:click="$set('showPlotsModal', false)" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700">
                            {{ __('Back to Activity') }}
                        </button>
                    </div>

                </div>
            </div>
        @endteleport
    @endif

    <!-- MODAL: CREATE NEW USER -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-800 max-w-md w-full space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-white">{{ __('Add New User') }}</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form wire:submit="createUser" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Full Name') }}</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                        @error('name') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Email Address') }}</label>
                        <input type="email" wire:model="email" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                        @error('email') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Phone Number (Optional)') }}</label>
                        <input type="text" wire:model="phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Select Role') }}</label>
                        <select wire:model="role" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none">
                            <option value="user">{{ __('Regular User') }}</option>
                            <option value="town_owner">{{ __('Town Scheme Owner') }}</option>
                            <option value="agent">{{ __('Property Agent') }}</option>
                            <option value="admin">{{ __('Administrator') }}</option>
                        </select>
                        @error('role') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Password') }}</label>
                        <input type="password" wire:model="password" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                        @error('password') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 hover:bg-amber-600 shadow-md">{{ __('Create User') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: CHANGE PASSWORD -->
    @if ($showPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-800 max-w-sm w-full space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white">{{ __('Change Password') }}</h3>
                    <button wire:click="$set('showPasswordModal', false)" class="text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form wire:submit="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('New Password') }}</label>
                        <input type="password" wire:model="new_password" placeholder="{{ __('At least 8 characters') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                        @error('new_password') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showPasswordModal', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 hover:bg-amber-600 shadow-md">{{ __('Save Password') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: WALLET ADJUSTMENT -->
    @if ($showWalletModal && $walletUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-800 max-w-sm w-full space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-white">{{ __('Adjust Wallet') }}</h3>
                        <p class="text-[11px] text-slate-400">{{ $walletUser->name }} · {{ __('Balance') }}: Rs. {{ number_format((float) $walletUser->wallet_balance) }}</p>
                    </div>
                    <button wire:click="$set('showWalletModal', false)" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-base"></i></button>
                </div>

                <form wire:submit="adjustWallet" class="space-y-4">
                    <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                        <button type="button" wire:click="$set('walletDirection', 'credit')" class="py-2 rounded-xl border {{ $walletDirection === 'credit' ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-950 text-slate-400 border-slate-800' }}">+ {{ __('Credit') }}</button>
                        <button type="button" wire:click="$set('walletDirection', 'debit')" class="py-2 rounded-xl border {{ $walletDirection === 'debit' ? 'bg-rose-600/20 text-rose-300 border-rose-500/40' : 'bg-slate-950 text-slate-400 border-slate-800' }}">− {{ __('Debit') }}</button>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Amount (Rs)') }}</label>
                        <input type="number" min="1" step="1" wire:model="walletAmount" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                        @error('walletAmount') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">{{ __('Reason (saved in the audit log)') }}</label>
                        <input type="text" wire:model="walletReason" maxlength="255" placeholder="{{ __('e.g. Refund for failed unlock') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 outline-none" />
                        @error('walletReason') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showWalletModal', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800">{{ __('Cancel') }}</button>
                        <button type="submit" wire:confirm="{{ __('Update this wallet balance?') }}" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 hover:bg-amber-600 shadow-md">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>