@php
    $locale = app()->getLocale();
    $isUr   = $locale === 'ur';
    $t      = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $pageTitle = $title ?? 'Assan Zameen | Rahim Yar Khan mein Plots, Houses aur Shops for Sale & Rent';
    $pageDescription = $description ?? 'Rahim Yar Khan mein verified plots, houses aur shops for sale & rent. Agents aur housing societies ki directory, plus free tax, registry aur land converter tools.';
    $pageImage = asset('public/hero-image.jpg');
    $pageUrl   = url()->current();

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => 'Assan Zameen',
        'url'      => url('/'),
        'logo'     => asset('public/favicon.png'),
        'sameAs'   => ['https://www.facebook.com/share/1bWhGHS5Vy/'],
    ];

    // Home page par dikhane wale top tools
    $showcaseTools = [
        ['route' => 'tools.legal-scanner',     'icon' => 'fa-shield-halved',         'badge' => 'AI',
            'ur' => 'AI پراپرٹی وکیل',            'en' => 'AI Property Wakeel',
            'desc_ur' => 'فرد، رجسٹری و ٹائٹل رسک اسکینر', 'desc_en' => 'Fard, registry & title risk scanner'],
        ['route' => 'tools.tax-calculator',    'icon' => 'fa-calculator',            'badge' => null,
            'ur' => 'ٹیکس و رجسٹری کیلکولیٹر',    'en' => 'Tax & Registry Calculator',
            'desc_ur' => 'Stamp Duty, TMA, FBR 236-C/K', 'desc_en' => 'Stamp duty, TMA, FBR 236-C/K'],
        ['route' => 'tools.land-converter',    'icon' => 'fa-ruler-combined',        'badge' => null,
            'ur' => 'پٹواری اراضی کنورٹر',        'en' => 'Patwari Land Converter',
            'desc_ur' => 'ٹیڑھے پلاٹ کا رقبہ، کرم، سرساہی و مرلہ', 'desc_en' => 'Irregular plot area, karam, sarsahi & marla'],
        ['route' => 'tools.bayana-generator',  'icon' => 'fa-file-signature',        'badge' => null,
            'ur' => 'اقرار نامہ بیعانہ میکر',     'en' => 'Bayana Agreement Maker',
            'desc_ur' => 'قانونی اسٹامپ پیپر ڈرافٹ جنریٹر', 'desc_en' => 'Stamp paper draft generator'],
        ['route' => 'tools.society-directory', 'icon' => 'fa-building-circle-check', 'badge' => null,
            'ur' => 'سوسائٹی NOC ڈائریکٹری',      'en' => 'Society NOC Directory',
            'desc_ur' => 'TMA, LDA, RDA منظور شدہ و غیر قانونی اسکیمیں', 'desc_en' => 'Approved & illegal schemes (TMA, LDA, RDA)'],
        ['route' => 'tools.rent-generator',    'icon' => 'fa-file-contract',         'badge' => null,
            'ur' => 'کرایہ نامہ میکر',            'en' => 'Rent Agreement Maker',
            'desc_ur' => 'پنجاب ٹیننسی ایکٹ و اسٹامپ ریڈی معاہدہ', 'desc_en' => 'Punjab Tenancy Act, stamp-ready agreement'],
    ];

    $steps = [
        ['icon' => 'fa-magnifying-glass', 'n' => '1',
            'title' => $t('Search', 'تلاش کریں'),
            'text'  => $t('Filter by purpose, type, housing scheme and size to find the right property.', 'مقصد، قسم، ہاؤسنگ سکیم اور سائز کے فلٹر سے اپنی مطلوبہ پراپرٹی تلاش کریں۔')],
        ['icon' => 'fa-handshake', 'n' => '2',
            'title' => $t('Connect', 'رابطہ کریں'),
            'text'  => $t('Contact the agent or town owner directly and arrange a visit.', 'ایجنٹ یا ٹاؤن اونر سے براہِ راست رابطہ کریں اور وزٹ طے کریں۔')],
        ['icon' => 'fa-clipboard-check', 'n' => '3',
            'title' => $t('Verify', 'تصدیق کریں'),
            'text'  => $t('Check tax, registry and society NOC with our free tools before you pay.', 'ادائیگی سے پہلے ہمارے مفت ٹولز سے ٹیکس، رجسٹری اور سوسائٹی NOC چیک کریں۔')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isUr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="color-scheme" content="dark">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    {{-- Open Graph / Twitter (WhatsApp & Facebook share preview) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Assan Zameen">
    <meta property="og:locale" content="{{ $isUr ? 'ur_PK' : 'en_US' }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <meta name="facebook-domain-verification" content="0euryzc4bk3zu3xheravcmf8iloetv">
    <meta name="google-adsense-account" content="ca-pub-4146136986292795">

    <link rel="icon" type="image/png" href="{{ asset('public/favicon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if($isUr)
        <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="preload" href="{{ asset('public/fonts/Jameel_Noori_Nastaleeq_Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    @endif

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; color-scheme: dark; }

        /* asset() use kiya hai taa-ke /tools/... jaise nested URLs par bhi font ka path sahi rahe */
        @font-face {
            font-family: 'Jameel Noori Nastaleeq';
            src: url('{{ asset('public/fonts/Jameel_Noori_Nastaleeq_Regular.woff2') }}') format('woff2'),
                 url('{{ asset('public/fonts/Jameel_Noori_Nastaleeq_Regular.woff') }}') format('woff'),
                 url('{{ asset('public/fonts/Jameel_Noori_Nastaleeq_Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: {!! $isUr ? "'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif" : "'Plus Jakarta Sans', sans-serif" !!} !important;
        }

        @if($isUr)
            /* Nastaleeq ko zyada line-height chahiye, warna nukte kat jate hain */
            body { line-height: 1.9; }
            input, button, select, textarea, h1, h2, h3, h4, h5, h6, p, span, a, li, label {
                font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif !important;
            }
        @endif

        /* ---- Calm visual system: kam glow, kam motion, naram contrast ---- */
        .az-ambient {
            background:
                radial-gradient(60rem 28rem at 50% -8%, rgba(251, 191, 36, .05), transparent 70%),
                linear-gradient(to bottom, #0f172a, #0b1220);
        }

        @keyframes azFadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        .az-fade-up { animation: azFadeUp .5s ease-out both; }

        /* Purane classes (header/footer mein use hue) ab bina chamak ke */
        .az-shine::after { display: none; }
        .az-grid { background: none; }

        .az-card { transition: background-color .2s ease, border-color .2s ease; }
        .az-card:hover { background-color: rgba(30, 41, 59, .65); border-color: rgba(148, 163, 184, .35); }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 999px; border: 2px solid #0f172a; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
        }
    </style>

    {{-- AdSense sirf live site par load hoga --}}
    @production
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4146136986292795" crossorigin="anonymous"></script>
    @endproduction

    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</head>
<body class="az-ambient min-h-screen flex flex-col bg-slate-900 text-slate-200 font-sans antialiased selection:bg-amber-400/80 selection:text-slate-900">

    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:bg-amber-500 focus:text-slate-950 focus:font-semibold focus:px-4 focus:py-2 focus:rounded-lg">
        {{ $t('Skip to content', 'مواد پر جائیں') }}
    </a>

    <!-- Top Navigation Header -->
    @include('partials.frontend-header')

    <main id="main-content" class="flex-1">

        <!-- Dynamic Hero, Search Filter, Plots Section -->
        <livewire:frontend-home />

        <!-- Free Smart Tools -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-4" aria-labelledby="home-tools-heading">
            <div class="max-w-2xl mb-10">
                <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">{{ $t('Free Smart Tools', 'مفت اسمارٹ ٹولز') }}</p>
                <h2 id="home-tools-heading" class="text-2xl sm:text-3xl font-bold text-slate-50 leading-snug">
                    {{ $t('Everything you need before you buy, sell or rent', 'خریدنے، بیچنے یا کرائے پر دینے سے پہلے جو کچھ درکار ہے') }}
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed mt-3">
                    {{ $t('Registry tax, land measurement, agreements and title checks — free, in Urdu and English.', 'رجسٹری ٹیکس، زمین کی پیمائش، معاہدے اور ٹائٹل چیک — مفت، اردو اور انگریزی میں۔') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($showcaseTools as $i => $tool)
                    <a href="{{ route($tool['route']) }}" wire:navigate
                       style="animation-delay: {{ $i * 50 }}ms"
                       class="az-card az-fade-up group flex items-start gap-4 p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                        <div class="w-11 h-11 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $tool['icon'] }}" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-[15px] font-semibold text-slate-50">{{ $isUr ? $tool['ur'] : $tool['en'] }}</h3>
                                @if($tool['badge'])
                                    <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded bg-amber-400/15 text-amber-300">{{ $tool['badge'] }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-400 leading-relaxed mt-1">{{ $isUr ? $tool['desc_ur'] : $tool['desc_en'] }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[11px] text-slate-600 group-hover:text-amber-300 mt-1 rtl:rotate-180 transition-colors" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- How it works -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20" aria-labelledby="home-steps-heading">
            <div class="max-w-2xl mb-10">
                <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">{{ $t('Simple process', 'آسان طریقہ') }}</p>
                <h2 id="home-steps-heading" class="text-2xl sm:text-3xl font-bold text-slate-50">
                    {{ $t('From search to a safe deal in three steps', 'تلاش سے محفوظ سودے تک تین مراحل') }}
                </h2>
            </div>

            <ol class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($steps as $step)
                    <li class="p-6 rounded-2xl bg-slate-800/30 border border-slate-700/40">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-full bg-amber-400/10 text-amber-300 text-sm font-bold flex items-center justify-center">{{ $step['n'] }}</span>
                            <i class="fa-solid {{ $step['icon'] }} text-slate-500" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-semibold text-slate-50 mb-1.5">{{ $step['title'] }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        <!-- Features Overview Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 grid grid-cols-1 md:grid-cols-3 gap-4" aria-label="Assan Zameen">
            <div class="p-6 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                <div class="w-11 h-11 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-house-chimney" aria-hidden="true"></i>
                </div>
                <h2 class="text-base font-semibold text-slate-50 mb-1.5">{{ __("Property Sale & Rent") }}</h2>
                <p class="text-slate-400 text-sm leading-relaxed">{{ __("Browse verified listings for apartments, houses, and commercial spaces with ease.") }}</p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                <div class="w-11 h-11 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                </div>
                <h2 class="text-base font-semibold text-slate-50 mb-1.5">{{ __("Towns & Plot Inventories") }}</h2>
                <p class="text-slate-400 text-sm leading-relaxed">{{ __("Explore complete housing schemes, available plots, map information, and installment options.") }}</p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                <div class="w-11 h-11 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-handshake" aria-hidden="true"></i>
                </div>
                <h2 class="text-base font-semibold text-slate-50 mb-1.5">{{ __("Verified Agents & Direct Contact") }}</h2>
                <p class="text-slate-400 text-sm leading-relaxed">{{ __("Connect directly with trusted property agents and town developers after a simple unlock fee.") }}</p>
            </div>
        </section>

    </main>

    <!-- Modern Dark Footer -->
    @include('partials.frontend-footer')

    @livewireScripts
</body>
</html>