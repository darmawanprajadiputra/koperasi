<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Koperasi</title>

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
    <div class="dashboard-container">

        @include('layouts.sidebar')

        <main class="dashboard-main">

            <header
                class="dashboard-header sticky top-0 z-30 flex items-center justify-between px-8 py-4 bg-white border-b border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <h2 class="text-3xl font-extrabold text-teal-900 text-primary uppercase tracking-wide">
                        @yield('page_title')
                    </h2>
                </div>

                <!-- Profile -->
                <div class="relative">
                    <button id="profileButton"
                        class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-gray-100 transition-all group">
                        <span
                            class="material-symbols-outlined text-teal-700 group-hover:text-teal-900 transition-colors"
                            style="font-size: 32px;">account_circle</span>
                        <span
                            class="text-sm font-semibold text-gray-700 group-hover:text-teal-900 transition-colors hidden sm:inline">
                            {{ Auth::user()->username }}
                        </span>
                        <span
                            class="material-symbols-outlined text-gray-400 group-hover:text-teal-700 transition-colors"
                            style="font-size: 18px;">expand_more</span>
                    </button>

                    <div id="profileDropdown"
                        class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
                        <!-- Username -->
                        <div class="px-4 py-3 border-b border-gray-100 bg-teal-50">
                            <p class="text-xs text-teal-600 font-medium uppercase tracking-wider mb-0.5">Masuk sebagai
                            </p>
                            <p class="text-sm font-bold text-teal-900">{{ Auth::user()->username }}</p>
                        </div>
                        <!-- Logout -->
                        <button type="button" onclick="document.getElementById('logoutForm').submit()"
                            class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-all">
                            <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
                            Logout
                        </button>
                    </div>
                </div>
            </header>

            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    @stack('scripts')

    @vite(['resources/js/app.js'])

    <script>
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        profileButton.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            profileDropdown.classList.add('hidden');
        });
    </script>

</body>

</html>
