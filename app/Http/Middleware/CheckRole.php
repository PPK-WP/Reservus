<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pembatas akses berdasarkan peran (alias 'role').
 *
 * Pemakaian di route: ->middleware(['auth', 'role:admin'])
 *                     ->middleware('role:admin,petugas')
 */
class CheckRole
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Tamu → halaman login.
        if (! $user) {
            return redirect('/login');
        }

        // Akun pending/ditolak tidak boleh memakai aplikasi (aturan bisnis 10).
        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Akun Anda belum aktif. Hubungi admin.');
        }

        // Peran tidak sesuai → 403.
        if ($roles !== [] && ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
