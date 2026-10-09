<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ur' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('public/favicon.png') }}">
        <meta name="facebook-domain-verification" content="0euryzc4bk3zu3xheravcmf8iloetv" />

        <title>{{ config('app.name', 'Assan Zameen') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-950 text-slate-100">
            
            <!-- Language Switcher in Auth Form -->
            <div class="mb-4">
                <a href="{{ route('lang.switch', app()->getLocale() == 'ur' ? 'en' : 'ur') }}" class="text-xs font-bold text-amber-400 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-full hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-globe"></i>
                    <span>{{ app()->getLocale() == 'ur' ? 'English' : 'اردو' }}</span>
                </a>
            </div>

            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="w-20 h-20 fill-current text-amber-400" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden sm:rounded-2xl text-slate-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>