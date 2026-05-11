<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Jika user tidak authenticated, redirect ke login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Jika user memiliki salah satu dari roles yang diizinkan, lanjutkan request
        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika user tidak memiliki role yang diizinkan, return 403 Forbidden
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}