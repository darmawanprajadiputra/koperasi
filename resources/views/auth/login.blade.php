<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KOPERASI BISMILLAH INDONESIA SEJAHTERA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" type="image/png" href="{{ asset('assets/pictures/koperasi.png') }}">
</head>

<body>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-background">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover opacity-10"
                alt="overhead shot of fresh organic vegetables and artisanal produce in a minimalist rustic market setting with soft natural morning light"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAyhme9OjX3PSSBZde_ghNnXKSMbEdT8xknUv4QtgGCzDzmbCJpKNOSyWCb_pFNo81lieZ4S8ltrngxgdoP6tS7Hkup83XJb5UK2FKS6BQweMPiN-kbgOi0g7uYsS3moT3GXMIGKHjCCRJDcRdTTZ3vTyHbV_N-ixPnvdNye8HyLFMawqtjbyN6seaBTQjBrswrNMrdmFbgNrzQfAubasSrHgqlPSgjTrmfyHU0cu3RQZ0IZI5Cg6FaHLmBsdLW_SerjZw8hrl5rxV_">
            <div class="absolute inset-0 bg-gradient-to-b from-background/40 via-background to-background"></div>
        </div>

        <!-- Login Container -->
        <main
            class="relative w-full max-w-5xl h-[90vh] grid grid-cols-1 md:grid-cols-2 bg-surface-container-lowest rounded-xl shadow-2xl overflow-hidden mx-auto">

            <div class="bg-green-700 md:flex h-[90vh] flex-col justify-between p-14 overflow-y-auto">
                <div>
                    <h1 class="text-white text-3xl font-bold uppercase tracking-widest">Koperasi Bismillah Indonesia
                        Sejahtera</h1>
                </div>
                <div class="space-y-4">
                    <blockquote class="text-3xl text-white font-headline font-bold leading-tight">
                        "Menghubungkan kebutuhan masyarakat dengan layanan koperasi modern."
                    </blockquote>
                    <div class=" w-12 bg-tertiary-fixed"></div>
                </div>
                <div class="text-xs text-white opacity-60">
                    © 2026 Koperasi Bismillah Indonesia Sejahtera.
                </div>
            </div>

            <!-- Form Side -->
            <div class="md:p-14 h-[90vh] flex flex-col justify-center bg-surface-container-lowest">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-error/10 border border-error rounded-lg">
                        <p class="text-error text-sm font-medium">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4" id="loginForm">
                    @csrf

                    <!-- Username/Email -->
                    <div class="space-y-2">
                        <label
                            class="block font-label text-xs font-bold text-on-surface-variant uppercase tracking-widest"
                            for="identity">
                            Username
                        </label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">person</span>
                            <input
                                class="w-full pl-12 pr-4 py-4 border-none rounded-xl focus:ring-2 focus:ring-primary-container text-on-surface font-body transition-all @error('username') ring-2 ring-error @enderror"
                                id="identity" name="username" placeholder="Username" type="text"
                                value="{{ old('username') }}" required>
                        </div>
                        @error('username')
                            <p class="text-error text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label
                                class="block font-label text-xs font-bold text-on-surface-variant uppercase tracking-widest"
                                for="password">
                                Password
                            </label>
                            {{-- <a class="text-xs font-bold text-primary hover:underline underline-offset-4" href="#">
                            Forgot Password
                        </a> --}}
                        </div>
                        <div class="relative w-full">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input id="passwordInput" name="password" type="password" placeholder="Password"
                                class="w-full pl-12 pr-12 py-4 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary-container text-on-surface font-body transition-all">
                            <button type="button" id="togglePassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-outline hover:text-primary transition-colors">
                                <span class="material-symbols-outlined" id="visibilityIcon">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-error text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center space-x-3">
                        <input id="remember" name="remember" type="checkbox">
                        <label class="text-sm text-on-surface-variant font-medium select-none cursor-pointer"
                            for="remember">
                            Remember Me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="bg-green-700 w-full editorial-gradient text-white py-3 rounded-full font-headline font-bold text-lg shadow-xl hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span>Masuk</span>
                        <span class="material-symbols-outlined arrow-icon">arrow_forward</span>
                    </button>
                </form>

                {{-- <!-- Secondary Actions -->
            <div class="mt-12 pt-8 border-t border-outline-variant/20 text-center">
                <p class="text-on-surface-variant text-sm mb-4">Belum bergabung dengan koperasi?</p>
                <a href="{{ route('register') }}" class="px-8 py-3 border-2 border-primary-container text-primary-container font-headline font-bold rounded-full hover:bg-primary-container hover:text-white transition-all inline-block">
                    Daftar Anggota Baru
                </a>
            </div> --}}
            </div>
        </main>

        <!-- Decorative Chips -->
        <div class="hidden lg:block fixed -bottom-20 -left-20 w-80 h-80 bg-tertiary/5 rounded-full blur-3xl"></div>
        <div class="hidden lg:block fixed top-10 -right-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('visibilityIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    </script>
</body>

</html>
