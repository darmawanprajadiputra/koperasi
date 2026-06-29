<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    const MAX_ATTEMPTS = 3;
    const LOCKOUT_DURATION = 60;

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $throttleKey = 'login_' . strtolower($request->input('username')) . '_' . $request->ip();

        if (Cache::has($throttleKey . '_locked')) {
            $lockedUntil = Cache::get($throttleKey . '_locked');
            $secondsLeft = max(1, $lockedUntil - time());

            session(['lockout_seconds' => $secondsLeft]);

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'login' => "Akun terkunci karena terlalu banyak percobaan login gagal. Coba lagi dalam {$secondsLeft} detik.",
                ]);
        }

        $credentials = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            Cache::forget($throttleKey . '_attempts');
            Cache::forget($throttleKey . '_locked');
            session()->forget('lockout_seconds');

            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'Login berhasil!');
        }

        $attempts = Cache::get($throttleKey . '_attempts', 0) + 1;
        Cache::put($throttleKey . '_attempts', $attempts, now()->addMinutes(10));

        $sisaPercobaan = self::MAX_ATTEMPTS - $attempts;

        if ($attempts >= self::MAX_ATTEMPTS) {
            $lockedUntil = time() + self::LOCKOUT_DURATION;
            Cache::put($throttleKey . '_locked', $lockedUntil, now()->addSeconds(self::LOCKOUT_DURATION + 5));
            Cache::forget($throttleKey . '_attempts');

            session(['lockout_seconds' => self::LOCKOUT_DURATION]);

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'login' => 'Akun terkunci karena terlalu banyak percobaan login gagal. Coba lagi dalam ' . self::LOCKOUT_DURATION . ' detik.',
                ]);
        }

        session()->forget('lockout_seconds');

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => "Username atau password salah. Sisa percobaan: {$sisaPercobaan}",
            ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'nullable|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max'      => 'Username maksimal 255 karakter.',
            'username.unique'   => 'Username sudah digunakan, silakan pilih yang lain.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'member',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak ditemukan dalam sistem.',
        ]);

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'))
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Get current authenticated user.
     */
    public function user()
    {
        return response()->json(Auth::user());
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'nullable|email|max:255|unique:users,email,' . $user->id,
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max'      => 'Username maksimal 255 karakter.',
            'username.unique'   => 'Username sudah digunakan.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
        ]);

        $user->username = $validated['username'];
        if ($validated['email']) {
            $user->email = $validated['email'];
        }
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user,
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'current_password.required'      => 'Password lama tidak boleh kosong.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.required'              => 'Password baru tidak boleh kosong.',
            'password.confirmed'             => 'Konfirmasi password baru tidak cocok.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }
}