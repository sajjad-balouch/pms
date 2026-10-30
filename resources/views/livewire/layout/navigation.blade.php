<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-slate-900/90 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-50">
    <style>
        .text-gray-900 {
            color: rgb(252 211 77 / var(--tw-text-opacity, 1));
        }

        /* Breeze Dropdown wrapper container white background force-remove */
        .origin-top-left > div, 
        .origin-top-right > div,
        .origin-top > div,
        [x-show="open"] > div.bg-white {
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            
            <!-- Left Side Nav Links & Brand -->
            <div class="flex items-center gap-8">
                @php
                    $user = auth()->user();
                    $role = $user?->role ?? 'buyer';
                    
                    $dashboardRoute = match($role) {
                        'admin' => 'admin.dashboard',
                        'town_owner' => 'town_owner.dashboard',
                        'agent' => 'agent.dashboard',
                        default => 'dashboard',
                    };
                @endphp

                <!-- Application Brand / Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ $user ? route($dashboardRoute) : url('/') }}" wire:navigate class="flex items-center gap-3 group focus:outline-none">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-300">
                            <i class="fa-solid fa-city text-slate-950 text-lg"></i>
                        </div>
                        <span class="text-lg font-black tracking-wider text-white uppercase group-hover:text-amber-400 transition-colors">
                            Assan<span class="text-amber-400">Zameen</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                @auth
                    <div class="hidden lg:flex items-center space-x-2">
                        <!-- Dashboard -->
                        <x-nav-link :href="route($dashboardRoute)" :active="request()->routeIs('*dashboard')" wire:navigate 
                            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('*dashboard') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                            <i class="fa-solid fa-chart-pie mr-2 {{ request()->routeIs('*dashboard') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <!-- Town Owner Menu Items -->
                        @if($role === 'town_owner')
                            @if (\App\Support\Tenant::can('towns'))
                            <x-nav-link :href="route('town_owner.towns')" :active="request()->routeIs('town_owner.towns*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.towns*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-building mr-2 {{ request()->routeIs('town_owner.towns*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('My Housing Schemes') }}
                            </x-nav-link>
                            @endif

                            @if (\App\Support\Tenant::can('customers'))
                            <x-nav-link :href="route('town_owner.customers')" :active="request()->routeIs('town_owner.customers*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.customers*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-address-book mr-2 {{ request()->routeIs('town_owner.customers*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Customers') }}
                            </x-nav-link>
                            @endif

                            @if (\App\Support\Tenant::can('recovery'))
                            <x-nav-link :href="route('town_owner.recovery')" :active="request()->routeIs('town_owner.recovery*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.recovery*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-phone-volume mr-2 {{ request()->routeIs('town_owner.recovery*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Recovery') }}
                            </x-nav-link>
                            @endif

                            @if (\App\Support\Tenant::can('employees'))
                            <x-nav-link :href="route('town_owner.employees')" :active="request()->routeIs('town_owner.employees*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.employees*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-users mr-2 {{ request()->routeIs('town_owner.employees*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Employees') }}
                            </x-nav-link>
                            @endif

                            @if (\App\Support\Tenant::can('expenses'))
                            <x-nav-link :href="route('town_owner.expenses')" :active="request()->routeIs('town_owner.expenses*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.expenses*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-receipt mr-2 {{ request()->routeIs('town_owner.expenses*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Expenses') }}
                            </x-nav-link>
                            @endif

                            @if (\App\Support\Tenant::isOwner())
                            <x-nav-link :href="route('town_owner.team')" :active="request()->routeIs('town_owner.team*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.team*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-user-shield mr-2 {{ request()->routeIs('town_owner.team*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Team') }}
                            </x-nav-link>
                            @endif
                        @endif

                        <!-- Agent Menu Items -->
                        @if($role === 'agent')
                            <!-- Leads (Direct Link) -->
                            <x-nav-link :href="route('agent.leads')" :active="request()->routeIs('agent.leads.*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('agent.leads.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-bullhorn mr-2 {{ request()->routeIs('agent.leads.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Leads') }}
                            </x-nav-link>

                            <!-- Dropdown 1: Properties, Digital Card & B2B Exchange -->
                            @php
                                $isPropertiesActive = request()->routeIs('agent.properties.*') || request()->routeIs('agent.portfolio.settings') || request()->routeIs('agent.dealer.network');
                            @endphp
                            <x-dropdown align="left" width="60" contentClasses="py-0 !bg-transparent border-0 shadow-none">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 focus:outline-none {{ $isPropertiesActive ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                                        <i class="fa-solid fa-house text-sm {{ $isPropertiesActive ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                        <span>{{ __('Properties & Hub') }}</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl space-y-1.5 w-64">
                                        <!-- My Properties -->
                                        <a href="{{ route('agent.properties') }}" wire:navigate
                                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.properties.*') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-2.5">
                                                <i class="fa-solid fa-house-chimney text-amber-400"></i>
                                                <span>{{ __('My Properties') }}</span>
                                            </div>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 font-bold">List</span>
                                        </a>

                                        <!-- Digital Card -->
                                        <a href="{{ route('agent.portfolio.settings') }}" wire:navigate
                                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.portfolio.settings') ? 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-2.5">
                                                <i class="fa-solid fa-id-card-clip text-indigo-400"></i>
                                                <span>{{ __('Digital Visiting Card') }}</span>
                                            </div>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-bold">Web</span>
                                        </a>

                                        <!-- B2B Dealer Exchange -->
                                        <a href="{{ route('agent.dealer.network') }}" wire:navigate
                                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.dealer.network') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-2.5">
                                                <i class="fa-solid fa-handshake-angle text-amber-400"></i>
                                                <span>{{ __('B2B Dealer Exchange') }}</span>
                                            </div>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-400 text-slate-950 font-black">B2B</span>
                                        </a>
                                    </div>
                                </x-slot>
                            </x-dropdown>

                            <!-- Dropdown 2: Agent Tools & Calculators -->
                            @php
                                $isToolsActive = request()->routeIs('agent.calculator') || 
                                                 request()->routeIs('agent.bayana') || 
                                                 request()->routeIs('agent.poster.maker') || 
                                                 request()->routeIs('agent.commission.ledger') || 
                                                 request()->routeIs('agent.tax.calculator');
                            @endphp
                            <x-dropdown align="left" width="80" contentClasses="py-0 !bg-transparent border-0 shadow-none">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 focus:outline-none {{ $isToolsActive ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                                        <i class="fa-solid fa-screwdriver-wrench text-sm {{ $isToolsActive ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                        <span>{{ __('Tools & Calculators') }}</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2.5 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl space-y-1.5 w-80">
                                        <!-- 1. Patwari Calculator -->
                                        <a href="{{ route('agent.calculator') }}" wire:navigate
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.calculator') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-calculator text-xs"></i>
                                                </div>
                                                <span class="truncate text-white font-semibold">{{ __('Calculator') }}</span>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-400 font-bold shrink-0">Area</span>
                                        </a>

                                        <!-- 2. Bayana Agreement PDF -->
                                        <a href="{{ route('agent.bayana') }}" wire:navigate
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.bayana') ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-file-contract text-xs"></i>
                                                </div>
                                                <span class="truncate text-white font-semibold">{{ __('Affidavit') }}</span>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-400 text-slate-950 font-black shrink-0">PDF</span>
                                        </a>

                                        <!-- 3. Social Media Poster Maker -->
                                        <a href="{{ route('agent.poster.maker') }}" wire:navigate
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.poster.maker') ? 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                                                </div>
                                                <span class="truncate text-white font-semibold">{{ __('Social Posts') }}</span>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 font-bold shrink-0">HD</span>
                                        </a>

                                        <!-- 4. Munshi Khata & Commission Ledger -->
                                        <a href="{{ route('agent.commission.ledger') }}" wire:navigate
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.commission.ledger') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-book-bookmark text-xs"></i>
                                                </div>
                                                <span class="truncate text-white font-semibold">{{ __('Manage Ledger') }}</span>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-amber-400 text-slate-950 font-black shrink-0">Khata</span>
                                        </a>

                                        <!-- 5. Registry & PLRA Tax Calculator -->
                                        <a href="{{ route('agent.tax.calculator') }}" wire:navigate
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors {{ request()->routeIs('agent.tax.calculator') ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-stamp text-xs"></i>
                                                </div>
                                                <span class="truncate text-white font-semibold">{{ __('Registry Expense') }}</span>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold shrink-0">Govt</span>
                                        </a>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        @endif

                        <!-- Admin Menu Items -->
                        @if($role === 'admin')
                            <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users.*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-user-gear mr-2 {{ request()->routeIs('admin.users.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Users') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.towns')" :active="request()->routeIs('admin.towns.*')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.towns.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-layer-group mr-2 {{ request()->routeIs('admin.towns.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Housing Schemes') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.topup-requests')" :active="request()->routeIs('admin.topup-requests')" wire:navigate
                                class="relative px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.topup-requests') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-wallet mr-2 {{ request()->routeIs('admin.topup-requests') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Top-Ups') }}
                                @php
                                    $pendingCount = \App\Models\WalletTransaction::where('type', 'deposit')->where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="ml-2 px-2 py-0.5 text-[10px] font-black text-slate-950 bg-amber-400 rounded-full shadow-md shadow-amber-400/20 animate-pulse">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </x-nav-link>

                            <x-nav-link :href="route('admin.payment-methods')" :active="request()->routeIs('admin.payment-methods')" wire:navigate
                                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.payment-methods') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-credit-card mr-2 {{ request()->routeIs('admin.payment-methods') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Payment Methods') }}
                            </x-nav-link>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Right Side Profile Settings / Public Links -->
            <div class="hidden lg:flex lg:items-center lg:ms-6 gap-3">
                <!-- Language switcher -->
                <div class="flex items-center bg-slate-800/70 border border-slate-700/70 rounded-xl p-1 text-xs" role="group" aria-label="Language">
                    <a href="{{ route('lang.switch', 'en') }}" lang="en"
                       class="px-2.5 py-1 rounded-lg font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">EN</a>
                    <a href="{{ route('lang.switch', 'ur') }}" lang="ur"
                       class="px-2.5 py-1 rounded-lg font-bold transition-all {{ app()->getLocale() === 'ur' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">اردو</a>
                </div>
                @auth
                    <x-dropdown align="right" width="56" contentClasses="py-0 !bg-transparent border-0 shadow-none">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 px-3.5 py-2 rounded-2xl border border-slate-800 bg-slate-900/90 hover:bg-slate-800/80 hover:border-slate-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-400/20">
                                <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-black text-amber-400 text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-bold text-white leading-tight" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                                    <div class="text-[10px] font-semibold text-amber-400 capitalize">
                                        {{ str_replace('_', ' ', auth()->user()->role ?? 'User') }}
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-1"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="p-2 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl space-y-1 w-56">
                                <div class="px-3 py-2 border-b border-slate-800/80">
                                    <p class="text-xs text-slate-400">{{ __('Signed in as') }}</p>
                                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <button wire:click="logout" class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors cursor-pointer">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    {{ __('Log Out') }}
                                </button>
                            </div>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-slate-300 hover:text-white transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-950 transition-all shadow-lg shadow-amber-400/10">Register</a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Hamburger (Mobile Toggle Button) -->
            <div class="flex items-center lg:hidden">
                <button @click="open = ! open" class="p-2.5 rounded-xl text-slate-400 hover:text-white bg-slate-800/60 focus:outline-none transition-colors">
                    <i class="fa-solid" :class="{'fa-xmark text-lg': open, 'fa-bars text-lg': !open}"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-slate-900 border-b border-slate-800 px-4 pt-2 pb-6 space-y-3">
        @auth
            <div class="space-y-1">
                <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('*dashboard') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-chart-pie text-amber-400"></i> {{ __('Dashboard') }}
                </a>

                @if($role === 'town_owner')
                    @if (\App\Support\Tenant::can('towns'))
                    <a href="{{ route('town_owner.towns') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.towns*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-building text-amber-400"></i> {{ __('My Housing Schemes') }}
                    </a>
                    @endif
                    @if (\App\Support\Tenant::can('customers'))
                    <a href="{{ route('town_owner.customers') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.customers*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-address-book text-amber-400"></i> {{ __('Customers') }}
                    </a>
                    @endif
                    @if (\App\Support\Tenant::can('recovery'))
                    <a href="{{ route('town_owner.recovery') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.recovery*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-phone-volume text-amber-400"></i> {{ __('Recovery Desk') }}
                    </a>
                    @endif
                    @if (\App\Support\Tenant::can('employees'))
                    <a href="{{ route('town_owner.employees') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.employees*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-users text-amber-400"></i> {{ __('Employees Management') }}
                    </a>
                    @endif
                    @if (\App\Support\Tenant::can('expenses'))
                    <a href="{{ route('town_owner.expenses') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.expenses*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-receipt text-amber-400"></i> {{ __('Town Expenses') }}
                    </a>
                    @endif
                    @if (\App\Support\Tenant::isOwner())
                    <a href="{{ route('town_owner.team') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.team*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-user-shield text-amber-400"></i> {{ __('My Team') }}
                    </a>
                    @endif
                @endif

                @if($role === 'agent')
                    <!-- Leads -->
                    <a href="{{ route('agent.leads') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('agent.leads.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-bullhorn text-amber-400"></i> {{ __('Leads CRM') }}
                    </a>

                    <!-- Properties Section Header -->
                    <div class="pt-2 pb-1 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Properties & Network</div>

                    <a href="{{ route('agent.properties') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.properties.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-house text-amber-400"></i> {{ __('My Properties') }}
                    </a>
                    <a href="{{ route('agent.portfolio.settings') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.portfolio.settings') ? 'text-indigo-300 bg-indigo-500/15 border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-id-card-clip text-indigo-400"></i>
                            <span>{{ __('Digital Visiting Card') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-bold">Web</span>
                    </a>
                    <a href="{{ route('agent.dealer.network') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.dealer.network') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-handshake-angle text-amber-400"></i>
                            <span>{{ __('B2B Dealer Exchange') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-400 text-slate-950 font-black">B2B</span>
                    </a>

                    <!-- Tools Section Header -->
                    <div class="pt-2 pb-1 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Agent Pro Tools</div>

                    <a href="{{ route('agent.calculator') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.calculator') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-calculator text-amber-400"></i>
                            <span>{{ __('Calculator') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400 font-bold">Area</span>
                    </a>
                    <a href="{{ route('agent.bayana') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.bayana') ? 'text-emerald-300 bg-emerald-500/15 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-contract text-emerald-400"></i>
                            <span>{{ __('Affidavit') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-400 text-slate-950 font-black">PDF</span>
                    </a>
                    <a href="{{ route('agent.poster.maker') }}" wire:navigate
                       class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.poster.maker') ? 'text-indigo-300 bg-indigo-500/15 border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-400"></i>
                            <span>{{ __('Social Posts') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-bold">HD</span>
                    </a>
                    <a href="{{ route('agent.commission.ledger') }}" wire:navigate
                       class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.commission.ledger') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-book-bookmark text-amber-400"></i>
                            <span>{{ __('Manage Ledger') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-400 text-slate-950 font-black">Khata</span>
                    </a>
                    <a href="{{ route('agent.tax.calculator') }}" wire:navigate
                       class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('agent.tax.calculator') ? 'text-emerald-300 bg-emerald-500/15 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-stamp text-emerald-400"></i>
                            <span>{{ __('Registry Expense') }}</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold">Govt</span>
                    </a>
                @endif

                @if($role === 'admin')
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('admin.users.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-user-gear text-amber-400"></i> {{ __('User Management') }}
                    </a>
                @endif
            </div>

            <!-- Mobile User Profile Details -->
            <div class="pt-4 border-t border-slate-800/80">
                <div class="px-4 mb-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-400 flex items-center justify-center font-black text-slate-950 text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 px-4 mb-2 text-xs" role="group" aria-label="Language">
                    <a href="{{ route('lang.switch', 'en') }}" lang="en" class="flex-1 text-center py-2 rounded-xl font-bold {{ app()->getLocale() === 'en' ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-slate-300' }}">English</a>
                    <a href="{{ route('lang.switch', 'ur') }}" lang="ur" class="flex-1 text-center py-2 rounded-xl font-bold {{ app()->getLocale() === 'ur' ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-slate-300' }}">اردو</a>
                </div>

                <button wire:click="logout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-500/10 cursor-pointer">
                    <i class="fa-solid fa-right-from-bracket"></i> {{ __('Log Out') }}
                </button>
            </div>
        @else
            <div class="space-y-2 pt-2">
                <a href="{{ route('login') }}" class="block text-center w-full py-2.5 rounded-xl text-xs font-bold text-slate-200 bg-slate-800">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block text-center w-full py-2.5 rounded-xl text-xs font-bold text-slate-950 bg-amber-400">Register</a>
                @endif
            </div>
        @endauth
    </div>
</nav>