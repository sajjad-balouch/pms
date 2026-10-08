<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ur' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? __('Assan Zameen | Exclusive Properties & Luxury Living') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('public/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles

    <style>
        /* Jameel Noori Nastaleeq Web Font */
        @font-face {
            font-family: 'Jameel Noori Nastaleeq';
            src: url('public/fonts/Jameel_Noori_Nastaleeq_Regular.woff2') format('woff2'),
                 url('public/fonts/Jameel_Noori_Nastaleeq_Regular.woff') format('woff'),
                 url('public/fonts/Jameel_Noori_Nastaleeq_Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: {{ app()->getLocale() == 'ur' ? "'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif" : "'Plus Jakarta Sans', sans-serif" }} !important;
        }

        @if(app()->getLocale() == 'ur')
            input, button, select, textarea, h1, h2, h3, h4, h5, h6, p, span, a {
                font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif !important;
            }
        @endif

        [x-cloak] {
            display: none !important;
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-100 font-sans antialiased">

    @include('partials.frontend-header')

    <main>
        {{ $slot }}
    </main>

    @include('partials.frontend-footer')

    @livewireScripts
</body>
</html>