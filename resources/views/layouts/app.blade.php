<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Assan Zameen') }}</title>

        <link rel="icon" type="image/png" href="{{asset('public/favicon.png')}}">
    
        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- FontAwesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Vite Assets & Livewire Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #090d16;
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
    <body class="font-sans antialiased bg-[#090d16] text-slate-100 selection:bg-amber-400 selection:text-slate-950">
        <!-- Ambient Glowing Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px]"></div>
            <div class="absolute top-1/3 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-[140px]"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Navigation Header -->
            @if(view()->exists('livewire.layout.navigation'))
                <livewire:layout.navigation />
            @endif

            <!-- Dynamic Header (If available) -->
            @if (isset($header))
                <header class="bg-slate-900/60 backdrop-blur-md border-b border-slate-800/80 sticky top-16 z-20 shadow-lg shadow-black/20">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Main Page Content Slot -->
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </main>
        </div>

        <!-- Fully Generic Livewire Script Endpoint -->
        <script
            src="{{ url('livewire/livewire.js') }}"
            data-csrf="{{ csrf_token() }}"
            data-update-uri="{{ url('livewire/update') }}"
            data-navigate-once>
        </script>
        @livewireScriptConfig

        
    </body>
</html>