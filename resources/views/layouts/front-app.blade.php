{{--
    Shared frontend layout.

    Har page (view ke andar) in variables se SEO control kar sakta hai, sab optional hain:
      $title, $description, $image, $canonical, $robots, $ogType, $ads

    Kisi page se pass karne ka aasan tareeqa (Livewire view ke PHP block mein):
      view()->share(['title' => '...', 'description' => '...', 'canonical' => '...']);
    Page-specific structured data (JSON-LD) ke liye view mein push('head') stack use karein.
--}}
@php
    $locale = app()->getLocale();
    $isUr   = $locale === 'ur';
    $t      = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $pageTitle       = $title ?? 'Assan Zameen | Rahim Yar Khan mein Plots, Houses aur Shops for Sale & Rent';
    $pageDescription = $description ?? 'Rahim Yar Khan mein verified plots, houses aur shops for sale & rent. Agents aur housing societies ki directory, plus free tax, registry aur land converter tools.';
    $pageImage       = $image ?? asset('public/hero-image.jpg');
    $pageUrl         = $canonical ?? url()->current();
    $pageOgType      = $ogType ?? 'website';
    $showAds         = $ads ?? true;

    // Private pages search engines se door rakhein
    $pageRobots = $robots ?? (
        request()->routeIs(['login', 'register', 'password.*', 'verification.*', 'dashboard*', 'admin.*', 'agent.*', 'town.*', 'saved-plots*'])
            ? 'noindex, nofollow'
            : 'index, follow, max-image-preview:large'
    );

    $orgSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => 'Assan Zameen',
        'url'      => url('/'),
        'logo'     => asset('public/favicon.png'),
        'sameAs'   => ['https://www.facebook.com/share/1bWhGHS5Vy/'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isUr ? 'rtl' : 'ltr' }}" class="scroll-smooth dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="color-scheme" content="dark">
    <meta name="robots" content="{{ $pageRobots }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    {{-- Open Graph / Twitter (WhatsApp & Facebook share preview) --}}
    <meta property="og:type" content="{{ $pageOgType }}">
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

    {{-- Alpine yahan se load nahi hota: Livewire 3 apna Alpine khud saath laata hai.
         Alag CDN Alpine lagane se "multiple instances of Alpine" ka masla aata hai. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; color-scheme: dark; }

        /* asset() use kiya hai taa-ke nested URLs par bhi font ka path sahi rahe */
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
            font-family: {!! $isUr ? "'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif" : "'Plus Jakarta Sans', system-ui, -apple-system, sans-serif" !!} !important;
            -webkit-font-smoothing: antialiased;
        }

        @if($isUr)
            /* Nastaleeq ko zyada line-height chahiye, warna nukte kat jate hain */
            body { line-height: 1.9; }
            input, button, select, textarea, h1, h2, h3, h4, h5, h6, p, span, a, li, label, dt, dd {
                font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif !important;
            }
            h1, h2, h3, h4 { line-height: 1.8; }
        @endif

        /* ---- Calm visual system: kam glow, kam motion, naram contrast ---- */
        .az-ambient {
            background:
                radial-gradient(60rem 28rem at 50% -8%, rgba(251, 191, 36, .05), transparent 70%),
                linear-gradient(to bottom, #0f172a, #0b1220);
        }

        @keyframes azFadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        .az-fade-up { animation: azFadeUp .5s ease-out both; }

        /* Header/footer mein use hone wale purane classes: ab bina chamak ke */
        .az-shine::after { display: none; }
        .az-grid { background: none; }

        .az-card { transition: background-color .2s ease, border-color .2s ease; }
        .az-card:hover { background-color: rgba(30, 41, 59, .65); border-color: rgba(148, 163, 184, .35); }

        ::selection { background-color: rgba(251, 191, 36, .8); color: #0f172a; }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 999px; border: 2px solid #0f172a; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
        }
    </style>

    {{-- AdSense sirf live site par (aur jab $ads false na ho) --}}
    @if($showAds)
        @production
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4146136986292795" crossorigin="anonymous"></script>
        @endproduction
    @endif

    <script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>

    {{-- Page-specific head content (JSON-LD, extra meta) --}}
    @stack('head')
</head>

<body class="az-ambient min-h-screen flex flex-col bg-slate-900 text-slate-200 font-sans antialiased selection:bg-amber-400/80 selection:text-slate-900">

    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:bg-amber-500 focus:text-slate-950 focus:font-semibold focus:px-4 focus:py-2 focus:rounded-lg">
        {{ $t('Skip to content', 'مواد پر جائیں') }}
    </a>

    @include('partials.frontend-header')

    <main id="main-content" class="flex-1">
        {{ $slot }}
    </main>

    @include('partials.frontend-footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>