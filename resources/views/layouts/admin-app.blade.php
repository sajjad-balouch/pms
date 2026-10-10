<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ur' ? 'rtl' : 'ltr' }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="facebook-domain-verification" content="0euryzc4bk3zu3xheravcmf8iloetv" />

    <title>{{ $title ?? __('Admin Dashboard') }} - {{ config('app.name', 'Property Portal') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('public/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @if(app()->getLocale() == 'ur')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @endif

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: {{ app()->getLocale() == 'ur' ? "'Noto Naskh Arabic', 'Jameel Noori Nastaleeq', sans-serif" : "'Plus Jakarta Sans', sans-serif" }} !important;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden bg-slate-950">

        <!-- Sidebar Overlay for Mobile -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             x-transition.opacity
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : ({{ app()->getLocale() == 'ur' ? "'translate-x-full'" : "'-translate-x-full'" }})" 
               class="fixed inset-y-0 {{ app()->getLocale() == 'ur' ? 'right-0 border-l' : 'left-0 border-r' }} z-50 w-64 bg-[#0a0f1d] border-slate-800/80 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between shadow-2xl">
            
            <div>
                <!-- Brand / Logo Header -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800/80 bg-[#0a0f1d]">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                            A
                        </div>
                        <span class="text-base font-extrabold text-white tracking-wide">{{ __('Control Panel') }}</span>
                    </a>
                    
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5 overflow-y-auto">
                    
                    <div class="px-3 pt-2 pb-2 text-[10px] font-black text-slate-400/90 uppercase tracking-widest">
                        {{ __('Main Management') }}
                    </div>

                    <!-- Dashboard Link -->
                    @php $isDashboard = request()->routeIs('admin.dashboard'); @endphp
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isDashboard ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line text-sm w-5 text-center {{ $isDashboard ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>

                    <!-- Cities Management Nav Link -->
                    <a href="{{ route('admin.cities') }}" 
                       wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.cities*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-900' }}">
                        <i class="fa-solid fa-city text-lg {{ request()->routeIs('admin.cities*') ? 'text-amber-400' : 'text-slate-500' }}"></i>
                        <span>{{ __('Cities') }}</span>
                    </a>

                    <a href="{{ route('admin.properties.create') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs sm:text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.properties.create') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-circle-plus text-base {{ request()->routeIs('admin.properties.create') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                        <span>{{ __('Add Property') }}</span>
                    </a>

                    <a href="{{ route('admin.analytics') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.analytics') ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie text-sm w-5 text-center"></i>
                        <span>{{ __('Page Analytics') }}</span>
                    </a>

                    <!-- Top-Up Requests Link -->
                    @php $isTopup = request()->routeIs('admin.topup-requests'); @endphp
                    <a href="{{ route('admin.topup-requests') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isTopup ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-wallet text-sm w-5 text-center {{ $isTopup ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span class="flex-1">{{ __('Top-Up Requests') }}</span>
                        @php
                            $pendingCount = \App\Models\WalletTransaction::where('type', 'deposit')->where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full {{ $isTopup ? 'bg-slate-950 text-amber-400' : 'bg-amber-500 text-slate-950' }}">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Contact Inquiries Link -->
                    @php $isInquiries = request()->routeIs('admin.contact-inquiries'); @endphp
                    <a href="{{ route('admin.contact-inquiries') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isInquiries ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-envelope-open-text text-sm w-5 text-center {{ $isInquiries ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span class="flex-1">{{ __('Manage Inquiries') }}</span>
                        @php
                            $pendingInquiriesCount = \App\Models\ContactInquiry::where('status', 'pending')->count();
                        @endphp
                        @if($pendingInquiriesCount > 0)
                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full {{ $isInquiries ? 'bg-slate-950 text-amber-400' : 'bg-amber-500 text-slate-950' }}">
                                {{ $pendingInquiriesCount }}
                            </span>
                        @endif
                    </a>

                    <!-- User Management Link -->
                    @php $isUsers = request()->routeIs('admin.users'); @endphp
                    <a href="{{ route('admin.users') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isUsers ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-users text-sm w-5 text-center {{ $isUsers ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span>{{ __('User Management') }}</span>
                    </a>

                    <!-- Housing Schemes Link -->
                    @php $isTowns = request()->routeIs('admin.towns'); @endphp
                    <a href="{{ route('admin.towns') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isTowns ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-building-user text-sm w-5 text-center {{ $isTowns ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span>{{ __('All Housing Schemes') }}</span>
                    </a>

                    <!-- Payment Methods Link -->
                    @php $isPayments = request()->routeIs('admin.payment-methods'); @endphp
                    <a href="{{ route('admin.payment-methods') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isPayments ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-credit-card text-sm w-5 text-center {{ $isPayments ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span>{{ __('Payment Methods') }}</span>
                    </a>

                    <!-- Content Management -->
                    <div class="px-3 pt-4 pb-2 text-[10px] font-black text-slate-400/90 uppercase tracking-widest">
                        {{ __('Content Management') }}
                    </div>

                    @php $isPages = request()->routeIs('admin.pages'); @endphp
                    <a href="{{ route('admin.pages') }}" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $isPages ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20 font-extrabold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-file-lines text-sm w-5 text-center {{ $isPages ? 'text-slate-950' : 'text-slate-400' }}"></i>
                        <span>{{ __('Pages Management') }}</span>
                    </a>

                    <!-- Property Management Section -->
                    <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        {{ __('Property Management') }}
                    </div>

                    <a href="{{ route('admin.properties') }}" 
                       wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.properties*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-900' }}">
                        <i class="fa-solid fa-building-circle-check text-lg {{ request()->routeIs('admin.properties*') ? 'text-amber-400' : 'text-slate-500' }}"></i>
                        <span>{{ __('All Properties') }}</span>
                    </a>

                    <a href="{{ route('admin.town-schemes') }}" 
                       wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.town-schemes*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-900' }}">
                        <i class="fa-solid fa-city text-lg {{ request()->routeIs('admin.town-schemes*') ? 'text-amber-400' : 'text-slate-500' }}"></i>
                        <span>{{ __('Town Schemes') }}</span>
                    </a>

                </nav>
            </div>

            <!-- Sidebar Footer User Profile -->
            <div class="p-4 border-t border-slate-800/80 bg-[#080c18]">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/60 border border-slate-800/50">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 font-black flex items-center justify-center uppercase shrink-0">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-xs font-bold text-white block truncate">{{ auth()->user()->name ?? __('Admin') }}</span>
                        <span class="text-[10px] font-medium text-slate-400 block truncate">{{ auth()->user()->email ?? '' }}</span>
                    </div>
                </div>
            </div>

        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col overflow-y-auto bg-slate-950">
            
            <!-- Top Navbar Header -->
            <header class="h-16 bg-[#0a0f1d]/90 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6">
                
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-xl bg-slate-800/80">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <span class="text-xs font-bold text-slate-400 hidden sm:inline-block tracking-wide">{{ __('Administrator Mode') }}</span>
                </div>

                <div class="flex items-center gap-3">
                    
                    <!-- Language Switcher -->
                    <div class="flex items-center bg-slate-900 border border-slate-800 rounded-xl px-2 py-1 text-xs">
                        <i class="fa-solid fa-globe text-amber-400 mr-1.5 ml-1.5"></i>
                        @if(app()->getLocale() == 'ur')
                            <a href="{{ route('lang.switch', 'en') }}" class="font-bold text-slate-300 hover:text-amber-400 px-1.5 py-0.5 rounded">English</a>
                        @else
                            <a href="{{ route('lang.switch', 'ur') }}" class="font-bold text-amber-400 hover:text-amber-300 px-1.5 py-0.5 rounded">اردو</a>
                        @endif
                    </div>

                    <!-- Front Site Button -->
                    <a href="{{ url('/') }}" target="_blank" class="text-xs font-bold text-slate-300 hover:text-amber-400 transition flex items-center gap-2 bg-slate-800/80 hover:bg-slate-800 px-3.5 py-2 rounded-xl border border-slate-700/60 shadow-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                        <span>{{ __('Visit Site') }}</span>
                    </a>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-rose-400 hover:text-white hover:bg-rose-600 transition flex items-center gap-2 bg-rose-500/10 px-3.5 py-2 rounded-xl border border-rose-500/20 shadow-sm">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>{{ __('Logout') }}</span>
                        </button>
                    </form>
                </div>

            </header>

            <!-- Page Content View -->
            <main class="flex-1">
                {{ $slot }}
            </main>

        </div>

    </div>

    @livewireScripts
</body>
</html>