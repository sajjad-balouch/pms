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
                    <div class="hidden lg:flex items-center space-x-1.5">
                        <!-- Dashboard -->
                        <x-nav-link :href="route($dashboardRoute)" :active="request()->routeIs('*dashboard')" wire:navigate 
                            class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('*dashboard') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                            <i class="fa-solid fa-chart-pie mr-2 {{ request()->routeIs('*dashboard') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <!-- Town Owner Menu Items -->
                        @if($role === 'town_owner')
                            <x-nav-link :href="route('town_owner.towns')" :active="request()->routeIs('town_owner.towns*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.towns*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-building mr-2 {{ request()->routeIs('town_owner.towns*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('My Housing Schemes') }}
                            </x-nav-link>

                            <x-nav-link :href="route('town_owner.employees')" :active="request()->routeIs('town_owner.employees*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.employees*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-users mr-2 {{ request()->routeIs('town_owner.employees*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Employees') }}
                            </x-nav-link>

                            <x-nav-link :href="route('town_owner.expenses')" :active="request()->routeIs('town_owner.expenses*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('town_owner.expenses*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-receipt mr-2 {{ request()->routeIs('town_owner.expenses*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Expenses') }}
                            </x-nav-link>
                        @endif

                        <!-- Agent Menu Items -->
                        @if($role === 'agent')
                            <x-nav-link :href="route('agent.leads')" :active="request()->routeIs('agent.leads.*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('agent.leads.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-bullhorn mr-2 {{ request()->routeIs('agent.leads.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Leads') }}
                            </x-nav-link>

                            <x-nav-link :href="route('agent.properties')" :active="request()->routeIs('agent.properties.*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('agent.properties.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-house mr-2 {{ request()->routeIs('agent.properties.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('My Properties') }}
                            </x-nav-link>
                        @endif

                        <!-- Admin Menu Items -->
                        @if($role === 'admin')
                            <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users.*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-user-gear mr-2 {{ request()->routeIs('admin.users.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Users') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.towns')" :active="request()->routeIs('admin.towns.*')" wire:navigate
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.towns.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-layer-group mr-2 {{ request()->routeIs('admin.towns.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Housing Schemes') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.topup-requests')" :active="request()->routeIs('admin.topup-requests')" wire:navigate
                                class="relative px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.topup-requests') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
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
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all duration-200 {{ request()->routeIs('admin.payment-methods') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30 shadow-md shadow-amber-500/5' : 'text-slate-300 hover:text-white hover:bg-slate-800/70 font-semibold' }}">
                                <i class="fa-solid fa-credit-card mr-2 {{ request()->routeIs('admin.payment-methods') ? 'text-amber-400' : 'text-slate-400' }}"></i>
                                {{ __('Payment Methods') }}
                            </x-nav-link>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Right Side Profile Settings / Public Links -->
            <div class="hidden lg:flex lg:items-center lg:ms-6">
                @auth
                    <x-dropdown align="right" width="56">
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
                            <div class="p-2 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl space-y-1">
                                <div class="px-3 py-2 border-b border-slate-800/80">
                                    <p class="text-xs text-slate-400">Signed in as</p>
                                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <button wire:click="logout" class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors">
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
                    <a href="{{ route('town_owner.towns') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.towns*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-building text-amber-400"></i> {{ __('My Housing Schemes') }}
                    </a>
                    <a href="{{ route('town_owner.employees') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.employees*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-users text-amber-400"></i> {{ __('Employees Management') }}
                    </a>
                    <a href="{{ route('town_owner.expenses') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('town_owner.expenses*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-receipt text-amber-400"></i> {{ __('Town Expenses') }}
                    </a>
                @endif

                @if($role === 'agent')
                    <a href="{{ route('agent.leads') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('agent.leads.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-bullhorn text-amber-400"></i> {{ __('Leads & Inquiries') }}
                    </a>
                    <a href="{{ route('agent.properties') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-extrabold {{ request()->routeIs('agent.leads.*') ? 'text-amber-300 bg-amber-500/15 border border-amber-500/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <i class="fa-solid fa-house mr-2 {{ request()->routeIs('agent.properties.*') ? 'text-amber-400' : 'text-slate-400' }}"></i>{{ __('My Properties') }}
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

                <button wire:click="logout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-500/10">
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