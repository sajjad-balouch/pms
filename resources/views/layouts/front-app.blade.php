<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ur' ? 'rtl' : 'ltr' }}" class="scroll-smooth dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="facebook-domain-verification" content="0euryzc4bk3zu3xheravcmf8iloetv" />
    
    <title>{{ $title ?? __('Assan Zameen | Exclusive Properties & Luxury Living') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('public/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Nastaliq+Urdu:wght@500;700&family=Noto+Sans+Arabic:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://quge5.com/88/tag.min.js" data-zone="302019" async data-cfasync="false"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles

    <style>
        /* Jameel Noori Nastaleeq Web Font */
        @font-face {
            font-family: 'Jameel Noori Nastaleeq';
            src: url('{{ asset("public/fonts/Jameel_Noori_Nastaleeq_Regular.woff2") }}') format('woff2'),
                 url("{{ asset('public/fonts/Jameel_Noori_Nastaleeq_Regular.woff') }}") format('woff'),
                 url("{{ asset('public/fonts/Jameel_Noori_Nastaleeq_Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --font-en: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-ur: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif;
        }

        body {
            background-color: #070a12 !important;
            color: #f8fafc !important;
            font-family: {{ app()->getLocale() == 'ur' ? "var(--font-ur)" : "var(--font-en)" }};
            -webkit-font-smoothing: antialiased;
        }

        @if(app()->getLocale() == 'ur')
            input, button, select, textarea, h1, h2, h3, h4, h5, h6, p, span, a, label {
                font-family: var(--font-ur) !important;
            }

            p {
                line-height: 2.2 !important;
                color: #cbd5e1;
            }

            h1, h2, h3, h4 {
                line-height: 2 !important;
            }
        @else
            p {
                color: #cbd5e1;
                line-height: 1.7;
            }
        @endif

        /* High Readability Selection */
        ::selection {
            background-color: #fbbf24;
            color: #020617;
        }

        [x-cloak] {
            display: none !important;
        }

        .glass-nav {
            background: rgba(10, 15, 26, 0.88);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        }

        /* Clean Scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #070a12;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>
</head>

<body class="bg-[#070a12] text-slate-100 antialiased min-h-screen flex flex-col justify-between">

    @include('partials.frontend-header')

    <main class="flex-grow">
        {{ $slot }}
    </main>

    @include('partials.frontend-footer')

    @livewireScripts
</body>
</html>