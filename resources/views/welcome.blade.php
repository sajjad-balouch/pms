<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ur' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Property Management System' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('public/favicon.png') }}">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased selection:bg-amber-500 selection:text-slate-900">

    <!-- Top Navigation Header -->
    @include('partials.frontend-header')

    <!-- Dynamic Hero, Search Filter, Plots Section -->
    <livewire:frontend-home />

    <!-- Features Overview Section -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700/60 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-2">🏘️ {{ __("Property Sale & Rent") }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ __("Browse verified listings for apartments, houses, and commercial spaces with ease.") }}</p>
        </div>

        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700/60 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-2">📐 {{ __("Towns & Plot Inventories") }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ __("Explore complete housing schemes, available plots, map information, and installment options.") }}</p>
        </div>

        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700/60 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-2">🤝 {{ __("Verified Agents & Direct Contact") }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ __("Connect directly with trusted property agents and town developers after a simple unlock fee.") }}</p>
        </div>
    </div>

    <!-- Modern Dark Footer -->
    @include('partials.frontend-footer')

    @livewireScripts
</body>
</html>