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

    <!-- CSS -->
    @vite(['resources/css/app.css'])
    @stack('head_data')
    @stack('styles')
</head>

<body class="bg-surface text-on-surface">
    <div class="dashboard-container">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="absolute top-8 right-8 z-40 flex items-center gap-4">
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileButton"
                        class="p-2 text-primary hover:bg-surface-container-high rounded-full transition-all">
                        <span class="material-symbols-outlined" style="font-size: 32px;">account_circle</span>
                    </button>

                    <div id="profileDropdown"
                        class="hidden absolute right-0 mt-2 w-48 bg-surface-container-low rounded-lg shadow-lg overflow-hidden z-50">
                        <!-- Username -->
                        <div class="px-4 py-3 border-b border-outline-variant">
                            <p class="text-sm font-semibold text-on-surface">{{ Auth::user()->username }}</p>
                        </div>
                        <!-- Logout -->
                        <button type="button" onclick="document.getElementById('logoutForm').submit()"
                            class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-surface-container hover:text-red-700 transition-all">
                            <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
                            Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile FAB -->
    <button class="mobile-fab">+</button>

    <!-- Logout Form (Hidden) -->
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Page-specific data (ADD_UNITS, EDIT_CONFIG, dll) -->
    @stack('scripts')

    <!-- JavaScript -->
    @vite(['resources/js/app.js'])

    <script>
        // Profile dropdown toggle
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        profileButton.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        // Tutup dropdown kalau klik di luar
        document.addEventListener('click', () => {
            profileDropdown.classList.add('hidden');
        });
    </script>

</body>

</html>