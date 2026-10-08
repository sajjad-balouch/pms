<header x-data="{ mobileOpen: false, userDropdown: false }" class="sticky top-0 z-50 glass-nav border-b border-slate-800/80 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    AZ
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-white block leading-none">{{__("ASSAN")}}<span class="text-amber-400">{{__("ZAMEEN")}}</span></span>
                    <span class="text-[10px] text-slate-400 tracking-widest uppercase">
                    {{ __("Property Portal") }}</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="{{ url('/') }}" class="hover:text-amber-400 transition-colors {{ request()->is('/') ? 'text-amber-400 font-semibold' : '' }}">{{ __('Home') }}</a>
                <a href="{{ route('housing-schemes') }}" class="hover:text-amber-400 transition-colors">{{ __('Housing Schemes') }}</a>
                <a href="{{ route('available-plots') }}" class="hover:text-amber-400 transition-colors">{{ __('Available Plots') }}</a>
                <a href="{{ route('about-us') }}" class="hover:text-amber-400 transition-colors">{{ __('About Us') }}</a>
                <a href="{{ route('contact') }}" class="hover:text-amber-400 transition-colors">{{ __('Contact') }}</a>
            </nav>

            <!-- User Auth Controls & Language Switcher -->
            <div class="hidden md:flex items-center gap-4">
                
                <!-- Desktop Language Switcher Button -->
                <div class="flex items-center bg-slate-800/90 border border-slate-700/80 rounded-xl p-1 text-xs">
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="px-2.5 py-1 rounded-lg font-bold transition-all {{ app()->getLocale() == 'en' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'ur') }}" 
                       class="px-2.5 py-1 rounded-lg font-bold transition-all {{ app()->getLocale() == 'ur' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">
                        اردو
                    </a>
                </div>

                @auth
                    @php
                        $userDashboardUrl = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'town_owner' => \Illuminate\Support\Facades\Route::has('town.dashboard') ? route('town.dashboard') : url('/dashboard'),
                            'agent' => \Illuminate\Support\Facades\Route::has('agent.dashboard') ? route('agent.dashboard') : url('/dashboard'),
                            default => url('/dashboard'),
                        };
                    @endphp

                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-3 bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 rounded-full px-4 py-2 text-sm text-slate-200 transition-all">
                            <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>

                        <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-56 bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 py-2 z-50 text-slate-300 text-sm">
                            <a href="{{ $userDashboardUrl }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 hover:text-white transition-colors">
                                <i class="fa-solid fa-gauge text-amber-400"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="/saved-plots" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 hover:text-white transition-colors">
                                <i class="fa-solid fa-bookmark text-amber-400"></i> {{ __('Saved Properties') }}
                            </a>
                            <div class="border-t border-slate-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                                    <i class="fa-solid fa-right-from-bracket"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-white font-medium text-sm px-4 py-2 transition-colors">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-semibold text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 hover:shadow-amber-500/30 transition-all scale-100 hover:scale-[1.02]">
                        {{ __('Get Started') }}
                    </a>
                @endauth
            </div>

            <!-- Mobile Actions (Lang + Hamburger) -->
            <div class="flex items-center gap-3 md:hidden">
                <!-- Mobile Language Quick Toggle Button -->
                <a href="{{ route('lang.switch', app()->getLocale() == 'ur' ? 'en' : 'ur') }}" 
                   class="px-2.5 py-1 rounded-lg border border-slate-700 bg-slate-800 text-amber-400 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-globe text-[11px]"></i>
                    <span>{{ app()->getLocale() == 'ur' ? 'EN' : 'اردو' }}</span>
                </a>

                <!-- Hamburger Button -->
                <button @click="mobileOpen = !mobileOpen" class="text-slate-300 hover:text-white p-2 text-xl">
                    <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars-staggered'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileOpen" x-cloak class="md:hidden bg-slate-900/95 border-b border-slate-800 px-6 py-6 space-y-4">
        
        <!-- Mobile Language Selector Row -->
        <div class="flex items-center justify-between bg-slate-950 border border-slate-800 p-2.5 rounded-xl text-xs">
            <span class="text-slate-400 font-semibold flex items-center gap-2">
                <i class="fa-solid fa-globe text-amber-400"></i> {{ __('Language') }}:
            </span>
            <div class="flex items-center gap-1">
                <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 rounded-lg font-bold transition-all {{ app()->getLocale() == 'en' ? 'bg-amber-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">English</a>
                <a href="{{ route('lang.switch', 'ur') }}" class="px-3 py-1 rounded-lg font-bold transition-all {{ app()->getLocale() == 'ur' ? 'bg-amber-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">اردو</a>
            </div>
        </div>

        <a href="{{ url('/') }}" class="block text-slate-200 font-medium hover:text-amber-400">{{ __('Home') }}</a>
        <a href="{{ route('housing-schemes') }}" class="block text-slate-200 font-medium hover:text-amber-400">{{ __('Housing Schemes') }}</a>
        <a href="{{ route('available-plots') }}" class="block text-slate-200 font-medium hover:text-amber-400">{{ __('Available Plots') }}</a>
        <a href="{{ route('about-us') }}" class="block text-slate-200 font-medium hover:text-amber-400">{{ __('About Us') }}</a>
        <a href="{{ route('contact') }}" class="block text-slate-200 font-medium hover:text-amber-400">{{ __('Contact') }}</a>
        
        <div class="pt-4 border-t border-slate-800 flex flex-col gap-3">
            @auth
                @php
                    $mobileDashboardUrl = match(auth()->user()->role) {
                        'admin' => route('admin.dashboard'),
                        'town_owner' => \Illuminate\Support\Facades\Route::has('town.dashboard') ? route('town.dashboard') : url('/dashboard'),
                        'agent' => \Illuminate\Support\Facades\Route::has('agent.dashboard') ? route('agent.dashboard') : url('/dashboard'),
                        default => url('/dashboard'),
                    };
                @endphp
                <a href="{{ $mobileDashboardUrl }}" class="w-full text-center bg-slate-800 text-white font-medium py-2.5 rounded-xl">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="w-full text-center border border-slate-700 text-slate-200 font-medium py-2.5 rounded-xl">{{ __('Sign In') }}</a>
                <a href="{{ route('register') }}" class="w-full text-center bg-amber-500 text-slate-950 font-semibold py-2.5 rounded-xl">{{ __('Get Started') }}</a>
            @endauth
        </div>
    </div>
</header>