<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko') - Koperasi</title>

    <!-- Fonts -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
    <link rel="icon" type="image/png" href="{{ asset('koperasi.png') }}">

    <!-- CSS -->
    @vite(['resources/css/app.css'])
    @stack('head_data')
    @stack('styles')
</head>

<body class="bg-surface text-on-surface">

    <!-- Navbar -->
    <header
        class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 bg-white border-b border-gray-100 shadow-sm">
        <a href="{{ route('shop') }}" class="flex items-center gap-2 text-teal-900 hover:opacity-80 transition-opacity">
            <img src="/assets/pictures/koperasi.png" alt="Koperasi Bismillah Indonesia Sejahtera"
                class="h-14 w-auto object-contain">
            <span class="font-manrope px-4 font-extrabold uppercase text-3xl tracking-wide">
                Koperasi Bismillah Indonesia Sejahtera
            </span>
        </a>
    </header>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    @stack('scripts')
    @vite(['resources/js/app.js'])

</body>

</html>
