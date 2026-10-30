@php
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Route;

    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    // Social links: sirf wohi dikhenge jin ki value set ho
    $socials = [
        ['url' => 'https://www.facebook.com/share/1bWhGHS5Vy/', 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
        ['url' => null, 'icon' => 'fa-instagram', 'label' => 'Instagram'],
        ['url' => null, 'icon' => 'fa-youtube',   'label' => 'YouTube'],
    ];
    $whatsapp = null; // e.g. '923001234567' (country code ke saath, + ke baghair)

    // Database queries 1 ghante ke liye cache hoti hain
    $footerTowns = Cache::remember('footer.towns', 3600, fn () => \App\Models\Town::latest()->take(5)->get(['id', 'name']));
    $footerPages = Cache::remember('footer.pages', 3600, fn () => \App\Models\Page::where('is_active', true)->get(['title', 'slug']));

    $footerTools = [
        ['route' => 'tools.legal-scanner',     'ur' => 'AI پراپرٹی وکیل',         'en' => 'AI Property Wakeel'],
        ['route' => 'tools.tax-calculator',    'ur' => 'ٹیکس و رجسٹری کیلکولیٹر',  'en' => 'Tax & Registry Calculator'],
        ['route' => 'tools.land-converter',    'ur' => 'پٹواری اراضی کنورٹر',      'en' => 'Patwari Land Converter'],
        ['route' => 'tools.bayana-generator',  'ur' => 'اقرار نامہ بیعانہ میکر',   'en' => 'Bayana Agreement Maker'],
        ['route' => 'tools.society-directory', 'ur' => 'سوسائٹی NOC ڈائریکٹری',    'en' => 'Society NOC Directory'],
        ['route' => 'tools.rent-generator',    'ur' => 'کرایہ نامہ میکر',          'en' => 'Rent Agreement Maker'],
    ];

    $hasNewsletter = Route::has('newsletter.subscribe');

    $ctaUrl = url('/dashboard');
    if (auth()->check()) {
        $ctaUrl = match (auth()->user()->role) {
            'admin'      => route('admin.dashboard'),
            'town_owner' => Route::has('town.dashboard') ? route('town.dashboard') : url('/dashboard'),
            'agent'      => Route::has('agent.dashboard') ? route('agent.dashboard') : url('/dashboard'),
            default      => url('/dashboard'),
        };
    }

    $linkClass = 'group inline-flex items-center gap-1.5 hover:text-amber-300 transition-colors';
@endphp

<footer class="relative bg-slate-950 border-t border-slate-800 text-slate-400 text-sm overflow-hidden">

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- CTA band --}}
        <div class="pt-16 pb-2">
            <div class="relative overflow-hidden rounded-3xl border border-slate-700/50 bg-slate-900/70 p-7 sm:p-10">
                
                <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="max-w-2xl">
                        <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-2">{{ $t('For agents & town owners', 'ایجنٹس اور ٹاؤن اونرز کے لیے') }}</p>
                        <h2 class="text-2xl sm:text-3xl font-bold text-slate-50 leading-snug">
                            {{ $t('Own a property or run a housing scheme?', 'کیا آپ کی پراپرٹی یا ہاؤسنگ سکیم ہے؟') }}
                        </h2>
                        <p class="text-slate-400 text-sm sm:text-base mt-2 leading-relaxed">
                            {{ $t('List it on Assan Zameen and reach buyers and tenants in Rahim Yar Khan.', 'اپنی لسٹنگ آسان زمین پر لگائیں اور رحیم یار خان کے خریداروں اور کرایہ داروں تک پہنچیں۔') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 shrink-0">
                        @auth
                            <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-3 rounded-xl transition-colors">
                                {{ __('Dashboard') }}
                                <i class="fa-solid fa-arrow-right text-xs rtl:rotate-180" aria-hidden="true"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-3 rounded-xl transition-colors">
                                {{ __('Get Started') }}
                                <i class="fa-solid fa-arrow-right text-xs rtl:rotate-180" aria-hidden="true"></i>
                            </a>
                        @endauth

                        @if($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 font-semibold px-6 py-3 rounded-xl border border-emerald-400/30 transition-colors">
                                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp
                            </a>
                        @else
                            <a href="{{ route('contact') }}"
                               class="inline-flex items-center gap-2 bg-slate-800/80 hover:bg-slate-800 text-white font-semibold px-6 py-3 rounded-xl border border-slate-700 transition-colors">
                                <i class="fa-solid fa-envelope text-amber-400" aria-hidden="true"></i> {{ __('Contact Us') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Link columns --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-x-8 gap-y-10 pt-12 pb-12 border-b border-slate-800/80">

            {{-- Brand + newsletter --}}
            <div class="col-span-2 space-y-5">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group" aria-label="Assan Zameen">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg">AZ</div>
                    <span dir="ltr" class="text-xl font-extrabold tracking-tight text-white">ASSAN<span class="text-amber-400">ZAMEEN</span></span>
                </a>

                <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                    {{ __("Assan Zameen real estate platform providing verified land ownership records, transparent plot transactions, and end-to-end legal support for buyers and investors.") }}
                </p>

                <div class="flex items-center gap-2.5">
                    @foreach($socials as $social)
                        @if($social['url'])
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"
                               class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-950 hover:bg-amber-400 hover:border-amber-400 transition-colors">
                                <i class="fa-brands {{ $social['icon'] }} text-sm" aria-hidden="true"></i>
                            </a>
                        @endif
                    @endforeach

                    @if($whatsapp)
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
                           class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-950 hover:bg-emerald-400 hover:border-emerald-400 transition-colors">
                            <i class="fa-brands fa-whatsapp text-base" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>

                @if($hasNewsletter)
                    <form method="POST" action="{{ route('newsletter.subscribe') }}" class="max-w-sm">
                        @csrf
                        <label for="footer-newsletter-email" class="block text-white font-semibold text-xs mb-2">{{ __('Subscribe Updates') }}</label>
                        <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-900 border border-slate-800 focus-within:border-amber-500/70 transition-colors">
                            <input id="footer-newsletter-email" type="email" name="email" required autocomplete="email"
                                   placeholder="{{ $t('Your email address', 'آپ کا ای میل') }}"
                                   class="flex-1 min-w-0 bg-transparent px-3 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none">
                            <button type="submit" class="shrink-0 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-4 py-2 rounded-xl transition-colors cursor-pointer">{{ __('Subscribe') }}</button>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">{{ __('Get notified on new plot releases and market insights.') }}</p>
                    </form>
                @endif
            </div>

            {{-- Housing societies (dynamic) --}}
            <nav aria-label="{{ __('Housing Societies') }}">
                <h2 class="text-white font-bold text-xs uppercase tracking-wider mb-5 flex items-center gap-2">
                    <span class="w-4 h-0.5 rounded-full bg-amber-500"></span>{{ __('Housing Societies') }}
                </h2>
                <ul class="space-y-3 text-xs">
                    @forelse($footerTowns as $fTown)
                        <li>
                            <a href="{{ route('housing-schemes', ['search' => $fTown->name]) }}" class="{{ $linkClass }}">
                                <i class="fa-solid fa-angle-right text-[10px] text-amber-500/70 rtl:rotate-180 group-hover:translate-x-0.5 transition-transform" aria-hidden="true"></i>
                                <span>{{ $fTown->name }}</span>
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('housing-schemes') }}" class="{{ $linkClass }}">{{ __('Housing Schemes') }}</a></li>
                    @endforelse
                </ul>
            </nav>

            {{-- Categories --}}
            <nav aria-label="{{ __('Categories') }}">
                <h2 class="text-white font-bold text-xs uppercase tracking-wider mb-5 flex items-center gap-2">
                    <span class="w-4 h-0.5 rounded-full bg-amber-500"></span>{{ __('Categories') }}
                </h2>
                <ul class="space-y-3 text-xs">
                    <li><a href="{{ route('available-plots', ['size' => '5_marla']) }}" class="{{ $linkClass }}">{{ __('5 Marla Plots') }}</a></li>
                    <li><a href="{{ route('available-plots', ['size' => '10_marla']) }}" class="{{ $linkClass }}">{{ __('10 Marla Plots') }}</a></li>
                    <li><a href="{{ route('available-plots', ['size' => '1_kanal']) }}" class="{{ $linkClass }}">{{ __('1 Kanal Residential') }}</a></li>
                    <li><a href="{{ route('available-plots', ['type' => 'commercial']) }}" class="{{ $linkClass }}">{{ __('Commercial Boulevard') }}</a></li>
                </ul>
            </nav>

            {{-- Free tools --}}
            <nav aria-label="{{ __('Tools') }}" class="col-span-2 lg:col-span-1">
                <h2 class="text-white font-bold text-xs uppercase tracking-wider mb-5 flex items-center gap-2">
                    <span class="w-4 h-0.5 rounded-full bg-amber-500"></span>{{ __('Tools') }}
                </h2>
                <ul class="space-y-3 text-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 sm:gap-x-6 lg:gap-x-0">
                    @foreach($footerTools as $tool)
                        <li><a href="{{ route($tool['route']) }}" class="{{ $linkClass }}">{{ $isUr ? $tool['ur'] : $tool['en'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
        </div>

        {{-- Bottom bar --}}
        <div class="py-8 space-y-5 text-xs">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-slate-500">&copy; {{ date('Y') }} {{ __('Assan Zameen. All rights reserved.') }}</p>

                <nav aria-label="Legal" class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                    @foreach($footerPages as $fPage)
                        @if($fPage->slug !== 'about-us')
                            <a href="{{ route('dynamic.page', $fPage->slug) }}" class="hover:text-amber-300 transition-colors">{{ __($fPage->title) }}</a>
                        @endif
                    @endforeach
                    <a href="{{ route('disclaimer') }}" class="hover:text-amber-300 transition-colors">{{ $t('Disclaimer', 'اعلانِ لاتعلقی') }}</a>
                    <a href="{{ route('about-us') }}" class="hover:text-amber-300 transition-colors">{{ __('About Us') }}</a>
                    <a href="{{ route('contact') }}" class="hover:text-amber-300 transition-colors">{{ __('Contact Us') }}</a>
                </nav>
            </div>

            <div class="flex items-start gap-3 rounded-2xl border border-slate-800 bg-slate-900/50 px-4 py-3">
                <i class="fa-solid fa-circle-info text-amber-500/80 mt-0.5" aria-hidden="true"></i>
                <p class="text-slate-500 leading-relaxed">
                    {{ __('Tools on this site are for general guidance only and are not legal or financial advice. Please consult a qualified lawyer or registrar before any property transaction.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Floating buttons --}}
    <button type="button"
            x-data="{ show: false }"
            x-show="show" x-cloak
            @scroll.window.passive="show = window.scrollY > 600"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-3"
            aria-label="{{ $t('Back to top', 'اوپر جائیں') }}"
            class="fixed bottom-5 ltr:right-5 rtl:left-5 z-40 w-11 h-11 rounded-full bg-amber-500 hover:bg-amber-400 text-slate-950 flex items-center justify-center cursor-pointer transition-colors">
        <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
    </button>

    @if($whatsapp)
        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
           class="fixed bottom-5 ltr:left-5 rtl:right-5 z-40 w-12 h-12 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white flex items-center justify-center text-2xl transition-colors">
            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
        </a>
    @endif
</footer>