@php
    $locale = app()->getLocale();
    $isUr   = $locale === 'ur';
    $t      = fn (string $en, string $ur) => $isUr ? $ur : $en;

    // Tools: desktop mega-menu aur mobile accordion, dono isi list se bante hain
    $tools = [
        ['route' => 'tools.legal-scanner',          'icon' => 'fa-shield-halved',         'badge' => 'AI', 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'AI پراپرٹی وکیل',               'en' => 'AI Property Wakeel',
            'desc_ur' => 'فرد، رجسٹری و ٹائٹل رسک اسکینر', 'desc_en' => 'Fard, registry & title risk scanner'],
        ['route' => 'tools.tax-calculator',         'icon' => 'fa-calculator',            'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'ٹیکس و رجسٹری کیلکولیٹر',       'en' => 'Tax & Registry Calculator',
            'desc_ur' => 'Stamp Duty, TMA, FBR 236-C/K', 'desc_en' => 'Stamp duty, TMA, FBR 236-C/K'],
        ['route' => 'tools.land-converter',         'icon' => 'fa-ruler-combined',        'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'پٹواری اراضی کنورٹر',           'en' => 'Patwari Land Converter',
            'desc_ur' => 'ٹیڑھے پلاٹ کا رقبہ، کرم، سرساہی و مرلہ', 'desc_en' => 'Irregular plot area, karam, sarsahi & marla'],
        ['route' => 'tools.bayana-generator',       'icon' => 'fa-file-signature',        'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'اقرار نامہ بیعانہ میکر',        'en' => 'Bayana Agreement Maker',
            'desc_ur' => 'قانونی اسٹامپ پیپر ڈرافٹ جنریٹر', 'desc_en' => 'Stamp paper draft generator'],
        ['route' => 'tools.society-directory',      'icon' => 'fa-building-circle-check', 'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'سوسائٹی NOC ڈائریکٹری',         'en' => 'Society NOC Directory',
            'desc_ur' => 'TMA, LDA, RDA منظور شدہ و غیر قانونی اسکیمیں', 'desc_en' => 'Approved & illegal schemes (TMA, LDA, RDA)'],
        ['route' => 'tools.inheritance-calculator', 'icon' => 'fa-scale-unbalanced-flip', 'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'وراثت و ترکہ تقسیم کیلکولیٹر',  'en' => 'Inheritance Calculator',
            'desc_ur' => 'شرعی احکام، پٹوار تقسیم و حصہ داران', 'desc_en' => 'Islamic shares, patwar division & heirs'],
        ['route' => 'tools.rent-generator',         'icon' => 'fa-file-contract',         'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'کرایہ نامہ میکر',               'en' => 'Rent Agreement Maker',
            'desc_ur' => 'پنجاب ٹیننسی ایکٹ و اسٹامپ ریڈی معاہدہ', 'desc_en' => 'Punjab Tenancy Act, stamp-ready agreement'],
        ['route' => 'tools.zakat-calculator',       'icon' => 'fa-hand-holding-heart',    'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'شرعی زکوٰۃ کیلکولیٹر',          'en' => 'Zakat Calculator',
            'desc_ur' => 'پلاٹ، نقدی، سونا و مالِ تجارت کا شرعی حساب', 'desc_en' => 'Plots, cash, gold & business assets'],
        ['route' => 'tools.cv-maker',               'icon' => 'fa-id-card',               'badge' => null, 'tint' => 'bg-amber-400/10 text-amber-300 ring-amber-300/15',
            'ur' => 'پیشہ ورانہ سی وی میکر',         'en' => 'Professional CV Maker',
            'desc_ur' => 'ATS فرینڈلی، لائیو پیش نظارہ و A4 پرنٹ', 'desc_en' => 'ATS-friendly, live preview, A4 print'],
    ];

    // Main navigation (desktop + mobile)
    $navLinks = [
        ['url' => url('/'),                 'icon' => 'fa-house',       'label' => __('Home'),            'active' => request()->is('/')],
        ['url' => route('housing-schemes'), 'icon' => 'fa-city',        'label' => __('Housing Schemes'), 'active' => request()->routeIs('housing-schemes*')],
        ['url' => route('available-plots'), 'icon' => 'fa-layer-group', 'label' => __('Available Plots'), 'active' => request()->routeIs('available-plots*')],
    ];

    $blogActive  = request()->routeIs('blog.*');
    $toolsActive = request()->is('tools*');

    $dashboardUrl = url('/dashboard');
    if (auth()->check()) {
        $dashboardUrl = match (auth()->user()->role) {
            'admin'      => route('admin.dashboard'),
            'town_owner' => \Illuminate\Support\Facades\Route::has('town.dashboard') ? route('town.dashboard') : url('/dashboard'),
            'agent'      => \Illuminate\Support\Facades\Route::has('agent.dashboard') ? route('agent.dashboard') : url('/dashboard'),
            default      => url('/dashboard'),
        };
    }
@endphp

<header
    x-data="{ mobileOpen: false, userDropdown: false, toolsDropdown: false, mobileToolsOpen: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 8"
    @scroll.window.passive="scrolled = window.scrollY > 8"
    @keydown.escape.window="mobileOpen = false; userDropdown = false; toolsDropdown = false"
    x-effect="document.body.classList.toggle('overflow-hidden', mobileOpen)"
    :class="scrolled || mobileOpen
        ? 'bg-slate-950/85 backdrop-blur-xl border-slate-800/80'
        : 'bg-slate-900/60 backdrop-blur-md border-transparent'"
    class="sticky top-0 z-50 border-b transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between transition-all duration-300" :class="scrolled ? 'h-16' : 'h-20'">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3 group" aria-label="Assan Zameen">
                <div class="relative">
                    <div class="relative w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg group-hover:bg-amber-400 transition-colors">
                        AZ
                    </div>
                </div>
                <div dir="ltr" class="leading-none">
                    <span class="text-xl font-extrabold tracking-tight text-white block">ASSAN<span class="text-amber-400">ZAMEEN</span></span>
                    <span class="text-[10px] text-slate-400 tracking-[0.25em] uppercase mt-1 block">Property Portal</span>
                </div>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-slate-300" aria-label="Main">

                @foreach($navLinks as $link)
                    <a href="{{ $link['url'] }}"
                       @if($link['active']) aria-current="page" @endif
                       class="relative flex items-center gap-2 py-2 hover:text-white transition-colors group {{ $link['active'] ? 'text-white' : '' }}">
                        <i class="fa-solid {{ $link['icon'] }} text-xs text-amber-400 " aria-hidden="true"></i>
                        <span>{{ $link['label'] }}</span>
                        <span class="absolute -bottom-0.5 inset-x-0 h-0.5 rounded-full bg-amber-400 origin-center transition-transform duration-300 {{ $link['active'] ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                    </a>
                @endforeach

                {{-- Smart Tools mega menu --}}
                <div class="relative" @click.away="toolsDropdown = false">
                    <button type="button"
                            @click="toolsDropdown = !toolsDropdown"
                            :aria-expanded="toolsDropdown.toString()"
                            aria-haspopup="true"
                            class="relative flex items-center gap-2 py-2 hover:text-white transition-colors group focus:outline-none focus-visible:text-white cursor-pointer {{ $toolsActive ? 'text-white' : '' }}">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs text-amber-400 " aria-hidden="true"></i>
                        <span>{{ __('Tools') }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"
                           :class="toolsDropdown ? 'rotate-180 text-amber-400' : ''" aria-hidden="true"></i>
                        <span class="absolute -bottom-0.5 inset-x-0 h-0.5 rounded-full bg-amber-400 transition-transform duration-300 origin-center"
                              :class="toolsDropdown || {{ $toolsActive ? 'true' : 'false' }} ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"></span>
                    </button>

                    {{-- Outer wrapper sirf position ke liye, taa-ke transition transform se na takraye --}}
                    <div class="absolute left-1/2 -translate-x-1/2 pt-4 z-50" x-show="toolsDropdown" x-cloak>
                        <div x-show="toolsDropdown"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="w-[min(36rem,calc(100vw-2rem))] rounded-3xl border border-slate-700/70 bg-slate-900/95 backdrop-blur-2xl shadow-2xl shadow-black/40 overflow-hidden">

                            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-800 bg-slate-800/30">
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $t('Smart Tools', 'اسمارٹ ٹولز') }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $t('Free tools for every property deal', 'ہر پراپرٹی سودے کے لیے مفت ٹولز') }}</p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg bg-emerald-500/15 text-emerald-300 border border-emerald-400/20">{{ $t('Free', 'مفت') }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-1 p-2">
                                @foreach($tools as $tool)
                                    @php $isActive = request()->routeIs($tool['route']); @endphp
                                    <a href="{{ route($tool['route']) }}" wire:navigate
                                       @if($isActive) aria-current="page" @endif
                                       class="group flex items-start gap-3 p-2.5 rounded-2xl transition-all {{ $isActive ? 'bg-amber-500/10 ring-1 ring-amber-400/30' : 'hover:bg-slate-800/80' }}">
                                        <div class="w-9 h-9 rounded-xl ring-1 flex items-center justify-center shrink-0 text-sm  {{ $tool['tint'] }}">
                                            <i class="fa-solid {{ $tool['icon'] }}" aria-hidden="true"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[13px] font-semibold text-white group-hover:text-amber-300 transition-colors truncate">{{ $isUr ? $tool['ur'] : $tool['en'] }}</span>
                                                @if($tool['badge'])
                                                    <span class="text-[8px] font-extrabold uppercase px-1 py-0.5 rounded bg-amber-500 text-slate-950">{{ $tool['badge'] }}</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $isUr ? $tool['desc_ur'] : $tool['desc_en'] }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('blog.index') }}" wire:navigate
                   @if($blogActive) aria-current="page" @endif
                   class="relative flex items-center gap-2 py-2 hover:text-white transition-colors group {{ $blogActive ? 'text-white' : '' }}">
                    <i class="fa-solid fa-newspaper text-xs text-amber-400 " aria-hidden="true"></i>
                    <span>{{ __('Our Blog') }}</span>
                    <span class="absolute -bottom-0.5 inset-x-0 h-0.5 rounded-full bg-amber-400 origin-center transition-transform duration-300 {{ $blogActive ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
            </nav>

            {{-- Language switcher + auth (desktop) --}}
            <div class="hidden lg:flex items-center gap-3">

                <div class="flex items-center bg-slate-800/70 border border-slate-700/70 rounded-xl p-1 text-xs" role="group" aria-label="Language">
                    <a href="{{ route('lang.switch', 'en') }}" lang="en"
                       class="px-2.5 py-1 rounded-lg font-bold transition-all {{ $locale === 'en' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">EN</a>
                    <a href="{{ route('lang.switch', 'ur') }}" lang="ur"
                       class="px-2.5 py-1 rounded-lg font-bold transition-all {{ $locale === 'ur' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">اردو</a>
                </div>

                @auth
                    <div class="relative" @click.away="userDropdown = false">
                        <button type="button" @click="userDropdown = !userDropdown"
                                :aria-expanded="userDropdown.toString()" aria-haspopup="true"
                                class="flex items-center gap-2.5 bg-slate-800/70 hover:bg-slate-800 border border-slate-700/70 rounded-full ps-1.5 pe-4 py-1.5 text-sm text-slate-200 transition-all cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center font-extrabold text-xs">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="userDropdown ? 'rotate-180' : ''" aria-hidden="true"></i>
                        </button>

                        <div x-show="userDropdown" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute ltr:right-0 rtl:left-0 mt-3 w-60 bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/40 border border-slate-700/70 p-2 z-50 text-slate-300 text-sm">
                            <div class="px-3 py-2.5 mb-1 rounded-xl bg-slate-800/60">
                                <p class="text-white font-semibold truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-400 capitalize">{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</p>
                            </div>
                            <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-colors">
                                <i class="fa-solid fa-gauge text-amber-400 w-4 text-center" aria-hidden="true"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ url('/saved-plots') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-colors">
                                <i class="fa-solid fa-bookmark text-amber-400 w-4 text-center" aria-hidden="true"></i> {{ __('Saved Properties') }}
                            </a>
                            <div class="border-t border-slate-800 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full ltr:text-left rtl:text-right flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-red-500/10 hover:text-red-400 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center" aria-hidden="true"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-white font-medium text-sm px-3 py-2 transition-colors">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                        {{ __('Get Started') }}
                        <i class="fa-solid fa-arrow-right text-xs rtl:rotate-180" aria-hidden="true"></i>
                    </a>
                @endauth
            </div>

            {{-- Mobile actions (language + hamburger) --}}
            <div class="flex items-center gap-2.5 lg:hidden">
                <a href="{{ route('lang.switch', $isUr ? 'en' : 'ur') }}"
                   class="px-3 py-2 rounded-xl border border-slate-700 bg-slate-800/80 text-amber-400 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-globe text-[11px]" aria-hidden="true"></i>
                    <span>{{ $isUr ? 'EN' : 'اردو' }}</span>
                </a>

                <button type="button" @click="mobileOpen = !mobileOpen"
                        :aria-expanded="mobileOpen.toString()" aria-controls="mobile-menu"
                        aria-label="Menu"
                        class="w-10 h-10 rounded-xl border border-slate-700 bg-slate-800/80 text-slate-200 hover:text-white flex items-center justify-center text-lg cursor-pointer">
                    <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars-staggered'" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>


    {{-- Mobile drawer --}}
    <div id="mobile-menu" x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden border-t border-slate-800 bg-slate-950/95 backdrop-blur-xl px-4 py-5 space-y-2 max-h-[calc(100dvh-4rem)] overflow-y-auto">

        @foreach($navLinks as $link)
            <a href="{{ $link['url'] }}"
               class="flex items-center justify-between gap-3 px-4 py-3 rounded-2xl font-medium transition-colors {{ $link['active'] ? 'bg-amber-500/10 text-amber-300 ring-1 ring-amber-400/25' : 'text-slate-200 hover:bg-slate-900' }}">
                <span class="flex items-center gap-3">
                    <i class="fa-solid {{ $link['icon'] }} text-amber-400 w-4 text-center" aria-hidden="true"></i>
                    <span>{{ $link['label'] }}</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-500 rtl:rotate-180" aria-hidden="true"></i>
            </a>
        @endforeach

        {{-- Tools accordion --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 overflow-hidden">
            <button type="button" @click="mobileToolsOpen = !mobileToolsOpen"
                    :aria-expanded="mobileToolsOpen.toString()"
                    class="w-full flex items-center justify-between px-4 py-3 font-medium text-slate-200 hover:text-amber-300 transition-colors cursor-pointer">
                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400 w-4 text-center" aria-hidden="true"></i>
                    <span>{{ $t('Smart Tools', 'اسمارٹ ٹولز') }}</span>
                </span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform"
                   :class="mobileToolsOpen ? 'rotate-180 text-amber-400' : ''" aria-hidden="true"></i>
            </button>

            <div x-show="mobileToolsOpen" x-cloak x-collapse.duration.200ms class="px-2 pb-2 space-y-1 border-t border-slate-800 pt-2">
                @foreach($tools as $tool)
                    @php $isActive = request()->routeIs($tool['route']); @endphp
                    <a href="{{ route($tool['route']) }}" wire:navigate
                       class="flex items-center gap-3 p-2.5 rounded-xl {{ $isActive ? 'bg-amber-500/10 ring-1 ring-amber-400/25' : 'hover:bg-slate-900' }}">
                        <div class="w-9 h-9 rounded-xl ring-1 flex items-center justify-center shrink-0 text-sm {{ $tool['tint'] }}">
                            <i class="fa-solid {{ $tool['icon'] }}" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-semibold text-white truncate">{{ $isUr ? $tool['ur'] : $tool['en'] }}</span>
                                @if($tool['badge'])
                                    <span class="text-[8px] font-extrabold uppercase px-1 py-0.5 rounded bg-amber-500 text-slate-950">{{ $tool['badge'] }}</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 line-clamp-1">{{ $isUr ? $tool['desc_ur'] : $tool['desc_en'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <a href="{{ route('blog.index') }}" wire:navigate
           class="flex items-center justify-between gap-3 px-4 py-3 rounded-2xl font-medium transition-colors {{ $blogActive ? 'bg-amber-500/10 text-amber-300 ring-1 ring-amber-400/25' : 'text-slate-200 hover:bg-slate-900' }}">
            <span class="flex items-center gap-3">
                <i class="fa-solid fa-newspaper text-amber-400 w-4 text-center" aria-hidden="true"></i>
                <span>{{ __('Our Blog') }}</span>
            </span>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500 rtl:rotate-180" aria-hidden="true"></i>
        </a>

        <div class="pt-4 mt-2 border-t border-slate-800 grid gap-3">
            @auth
                <a href="{{ $dashboardUrl }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-semibold py-3 rounded-2xl transition-colors">
                    <i class="fa-solid fa-gauge me-2 text-amber-400" aria-hidden="true"></i>{{ __('Dashboard') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-center border border-slate-700 text-slate-300 hover:text-red-400 font-medium py-3 rounded-2xl cursor-pointer transition-colors">{{ __('Logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full text-center border border-slate-700 text-slate-200 hover:bg-slate-900 font-medium py-3 rounded-2xl transition-colors">{{ __('Sign In') }}</a>
                <a href="{{ route('register') }}" class="w-full text-center bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3 rounded-2xl">{{ __('Get Started') }}</a>
            @endauth
        </div>
    </div>
</header>