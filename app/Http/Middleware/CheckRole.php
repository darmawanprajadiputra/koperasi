<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk mengecek role user.
 * 
 * Cara penggunaan di route:
 * Route::get('/dashboard', [...])
 *     ->middleware('role:user,admin');
 * 
 * atau untuk single role:
 * Route::get('/admin', [...])
 *     ->middleware('role:admin');
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah authenticated
        if (!$request->user()) {
            return redirect('login');
        }

        // Cek apakah user memiliki salah satu dari role yang diizinkan
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki role yang diizinkan, abort dengan 403
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
