<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Property Management System' }}</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased selection:bg-amber-500 selection:text-slate-900">

    <!-- Top Navigation Header -->
    @include('partials.frontend-header')

    <!-- Dynamic Hero, Search Filter, Plots Section -->
    <livewire:frontend-home />

    <!-- Features Overview Section -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700/60 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-2">🏘️ Property Sale & Rent</h3>
            <p class="text-slate-400 text-sm">Browse verified listings for apartments, houses, and commercial spaces with ease.</p>
        </div>

        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700/60 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-2">📐 Towns & Plot Inventories</h3>
            <p class="text-slate-400 text-sm">Explore complete housing schemes, available plots, map information, and installment options.</p>
        </div>

        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700/60 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-2">🤝 Verified Agents & Direct Contact</h3>
            <p class="text-slate-400 text-sm">Connect directly with trusted property agents and town developers after a simple unlock fee.</p>
        </div>
    </div>

    <!-- Modern Dark Footer -->
    @include('partials.frontend-footer')

    @livewireScripts
</body>
</html>