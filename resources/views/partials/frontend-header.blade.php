<header x-data="{ mobileOpen: false, userDropdown: false, toolsDropdown: false, mobileToolsOpen: false }" class="sticky top-0 z-50 glass-nav border-b border-slate-800/80 transition-all">
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
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-medium text-slate-300">
                <a href="{{ url('/') }}" class="flex items-center gap-1.5 hover:text-amber-400 transition-colors {{ request()->is('/') ? 'text-amber-400 font-semibold' : '' }}">
                    <i class="fa-solid fa-house text-xs text-amber-400"></i>
                    <span>{{ __('Home') }}</span>
                </a>

                <a href="{{ route('housing-schemes') }}" class="flex items-center gap-1.5 hover:text-amber-400 transition-colors {{ request()->routeIs('housing-schemes*') ? 'text-amber-400 font-semibold' : '' }}">
                    <i class="fa-solid fa-city text-xs text-amber-400"></i>
                    <span>{{ __('Housing Schemes') }}</span>
                </a>

                <a href="{{ route('available-plots') }}" class="flex items-center gap-1.5 hover:text-amber-400 transition-colors {{ request()->routeIs('available-plots*') ? 'text-amber-400 font-semibold' : '' }}">
                    <i class="fa-solid fa-layer-group text-xs text-amber-400"></i>
                    <span>{{ __('Available Plots') }}</span>
                </a>
                
                <!-- Smart Tools Dropdown (Desktop) -->
                <div class="relative" @click.away="toolsDropdown = false">
                    <button @click="toolsDropdown = !toolsDropdown" 
                            class="flex items-center gap-1.5 hover:text-amber-400 transition-colors py-2 focus:outline-none cursor-pointer {{ request()->is('tools*') ? 'text-amber-400 font-semibold' : '' }}">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs text-amber-400"></i>
                        <span>{{ __('Tools') }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="toolsDropdown ? 'rotate-180 text-amber-400' : ''"></i>
                    </button>

                    <!-- Dropdown Menu Box -->
                    <div x-show="toolsDropdown" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute left-0 mt-3 w-80 bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-800 p-2 z-50 space-y-1">
                        
                        <!-- Tool 1: AI Wakeel (Active Route) -->
                        <a href="{{ route('tools.legal-scanner') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.legal-scanner') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}" style="font-family: Noto Nastaliq Urdu">
                            <div class="w-9 h-9 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">AI پراپرٹی وکیل</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">فرد، رجسٹری و ٹائٹل رسک اسکینر</p>
                            </div>
                        </a>

                       <!-- Tool 2: Registry & FBR Tax Calculator (Active Route) -->
                        <a href="{{ route('tools.tax-calculator') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.tax-calculator') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}" style="font-family: Noto Nastaliq Urdu">
                            <div class="w-9 h-9 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">ٹیکس و رجسٹری کیلکولیٹر</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">Stamp Duty, TMA, FBR 236-C/K</p>
                            </div>
                        </a>

                        <!-- Tool 3: Patwari Land Converter (Active Route) -->
                        <a href="{{ route('tools.land-converter') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.land-converter') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}" style="font-family: Noto Nastaliq Urdu">
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-ruler-combined"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">پٹواری اراضی کنورٹر</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">ٹیڑھے پلاٹ کا رقبہ، کرم، سرساہی و مرلہ</p>
                            </div>
                        </a>

                        <!-- Tool 4: Legal Bayana Deed Generator (Active Route) -->
                        <a href="{{ route('tools.bayana-generator') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.bayana-generator') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}" style="font-family: Noto Nastaliq Urdu">
                            <div class="w-9 h-9 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">اقرار نامہ بیعانہ میکر</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">قانونی اسٹامپ پیپر ڈرافٹ جنریٹر</p>
                            </div>
                        </a>

                        <!-- Tool 5: Society NOC Verification (Active Route) -->
                        <a href="{{ route('tools.society-directory') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.society-directory') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}" style="font-family: Noto Nastaliq Urdu">
                            <div class="w-9 h-9 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-building-circle-check"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">سوسائٹی NOC ڈائریکٹری</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">TMA, LDA, RDA منظور شدہ و غیر قانونی اسکیمیں</p>
                            </div>
                        </a>
                        <!-- Tool 6: Islamic Inheritance Calculator (Active Route) -->
                        <a href="{{ route('tools.inheritance-calculator') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.inheritance-calculator') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}" style="font-family: Noto Nastaliq Urdu">
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-scale-unbalanced-flip"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">وراثت و ترکہ تقسیم کیلکولیٹر</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">شرعی احکام، پٹوار تقسیم و حصہ داران</p>
                            </div>
                        </a>
                        <!-- Tool 7: Rent Agreement Generator (Active Route) -->
                        <a href="{{ route('tools.rent-generator') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.rent-generator') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}">
                            <div class="w-9 h-9 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">کرایہ نامہ میکر (Rent Agreement)</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">پنجاب ٹیننسی ایکٹ و اسٹامپ ریڈی معاہدہ</p>
                            </div>
                        </a>
                        <!-- Tool 7: Zakat Calculator (Active Route) -->
                        <a href="{{ route('tools.zakat-calculator') }}" wire:navigate 
                           class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.zakat-calculator') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}">
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-hand-holding-heart"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">شرعی زکوٰۃ کیلکولیٹر</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">پلاٹ، نقدی، سونا و مالِ تجارت کا شرعی حساب</p>
                            </div>
                        </a>
                        <!-- Tool 8: Free ATS CV Maker (Active Route) -->
                    <a href="{{ route('tools.cv-maker') }}" wire:navigate 
                       class="flex items-start gap-3 p-2.5 rounded-xl transition-all group {{ request()->routeIs('tools.cv-maker') ? 'bg-amber-500/15 border border-amber-500/30' : 'hover:bg-slate-800/80' }}">
                        <div class="w-9 h-9 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 text-sm group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-file-user"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">پیشہ ورانہ سی وی میکر</span>
                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">ATS فرینڈلی، لائیو پیش نظارہ و A4 پرنٹ</p>
                        </div>
                    </a>
                        
                        
                    </div>
                </div>

                <a href="{{ route('blog.index') }}" 
                   wire:navigate 
                   class="flex items-center gap-1.5 hover:text-amber-400 transition-colors {{ request()->routeIs('blog.*') ? 'text-amber-400 font-semibold' : '' }}">
                    <i class="fa-solid fa-newspaper text-xs text-amber-400"></i>
                    <span>{{ __('Our Blog') }}</span>
                </a>

                
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
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-3 bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 rounded-full px-4 py-2 text-sm text-slate-200 transition-all cursor-pointer">
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
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 hover:bg-red-500/10 hover:text-red-400 transition-colors cursor-pointer">
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
                <button @click="mobileOpen = !mobileOpen" class="text-slate-300 hover:text-white p-2 text-xl cursor-pointer">
                    <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars-staggered'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileOpen" x-cloak class="md:hidden bg-slate-900/95 border-b border-slate-800 px-6 py-6 space-y-4 max-h-[85vh] overflow-y-auto">
        
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

        <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-slate-200 font-medium hover:text-amber-400 py-1">
            <i class="fa-solid fa-house text-amber-400 w-4 text-center"></i>
            <span>{{ __('Home') }}</span>
        </a>

        <a href="{{ route('housing-schemes') }}" class="flex items-center gap-2.5 text-slate-200 font-medium hover:text-amber-400 py-1">
            <i class="fa-solid fa-city text-amber-400 w-4 text-center"></i>
            <span>{{ __('Housing Schemes') }}</span>
        </a>

        <a href="{{ route('available-plots') }}" class="flex items-center gap-2.5 text-slate-200 font-medium hover:text-amber-400 py-1">
            <i class="fa-solid fa-layer-group text-amber-400 w-4 text-center"></i>
            <span>{{ __('Available Plots') }}</span>
        </a>

        <!-- Mobile Smart Tools Accordion -->
        <div class="border border-slate-800 rounded-2xl bg-slate-950/60 overflow-hidden my-1">
            <button @click="mobileToolsOpen = !mobileToolsOpen" class="w-full flex items-center justify-between p-3.5 text-xs font-bold text-slate-200 hover:text-amber-400 transition-colors cursor-pointer">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400 w-4 text-center"></i>
                    <span>{{ __('Smart Tools (اسمارٹ ٹولز)') }}</span>
                </span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="mobileToolsOpen ? 'rotate-180 text-amber-400' : ''"></i>
            </button>

            <div x-show="mobileToolsOpen" x-cloak class="px-3 pb-3 space-y-1.5 border-t border-slate-800/80 pt-2">
                <!-- AI Wakeel (Live Route) -->
                <a href="{{ route('tools.legal-scanner') }}" wire:navigate class="flex items-center justify-between p-2 rounded-xl bg-slate-900 text-xs font-bold text-amber-300 border border-amber-500/20">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-amber-400 w-4 text-center"></i>
                        <span>AI پراپرٹی وکیل (ٹائٹل اسکینر)</span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                <!-- Tax Calculator -->
                <a href="{{ route('tools.tax-calculator') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-sky-400 w-4 text-center"></i>
                        <span>ٹیکس و رجسٹری کیلکولیٹر</span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                <!-- Land Converter -->
                <a href="{{ route('tools.land-converter') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-ruler-combined text-emerald-400 w-4 text-center"></i>
                        <span>پٹواری اراضی کنورٹر</span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                <!-- Bayana Deed Maker -->
                <a href="{{ route('tools.bayana-generator') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-indigo-400 w-4 text-center"></i>
                        <span>اقرار نامہ بیعانہ میکر</span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                
                <!-- NOC Directory -->
                <a href="{{ route('tools.society-directory') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-purple-400 w-4 text-center"></i>
                        <span>سوسائٹی NOC ڈائریکٹری</span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>
                <!-- Inheritance calculator -->
                <a href="{{ route('tools.inheritance-calculator') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-purple-400 w-4 text-center"></i>
                        <span> وراثت حصص  </span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>
                <!-- Rent writer -->
                <a href="{{ route('tools.rent-generator') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-purple-400 w-4 text-center"></i>
                        <span> کریہ نامہ </span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>
                <!-- Zakat caculator -->
                <a href="{{ route('tools.zakat-calculator') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-purple-400 w-4 text-center"></i>
                        <span> ثکات نصاب  </span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>
                <!-- CV Maker-->
                <a href="{{ route('tools.cv-maker') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-900 text-xs text-slate-300">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-purple-400 w-4 text-center"></i>
                        <span> سی وی میکر   </span>
                    </span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

            </div>
        </div>

        <a href="{{ route('blog.index') }}" 
           wire:navigate 
           class="flex items-center gap-2.5 text-slate-200 font-medium hover:text-amber-400 py-1">
            <i class="fa-solid fa-newspaper text-amber-400 w-4 text-center"></i>
            <span>{{ __('Our Blog') }}</span>
        </a>

        
        
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