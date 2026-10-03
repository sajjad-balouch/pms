<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <title>{{ $title ?? 'Assan Zameen | Exclusive Properties & Luxury Living' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{asset('public/favicon.png')}}">
    @livewireStyles

    <style>
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