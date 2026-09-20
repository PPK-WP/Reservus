<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

/**
 * Masuk aplikasi (US-13, US-15).
 *
 * Hanya akun berstatus 'aktif' yang boleh memakai aplikasi (aturan bisnis 10);
 * akun 'pending' dan 'ditolak' dikeluarkan kembali beserta penjelasannya.
 */
class LoginController extends Controller implements HasMiddleware
{
    use AuthenticatesUsers;

    /**
     * Tujuan setelah berhasil masuk.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Middleware controller (Laravel 11+ : base Controller tidak lagi punya method middleware()).
     *
     * @return array<int, \Illuminate\Routing\Controllers\Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
            new Middleware('auth', only: ['logout']),
        ];
    }

    /**
     * Dipanggil setelah kredensial benar dan sesi diperbarui.
     * Mengembalikan response di sini membatalkan pengalihan bawaan.
     */
    protected function authenticated(Request $request, User $user): ?RedirectResponse
    {
        if ($user->isActive()) {
            return null;
        }

        $pesan = $user->status === 'pending'
            ? 'Akun Anda menunggu verifikasi admin.'
            : 'Pendaftaran Anda ditolak.'.($user->verification_note ? ' Catatan admin: '.$user->verification_note : '');

        $this->keluarkan($request);

        return redirect('/login')
            ->withInput($request->only($this->username()))
            ->withErrors([$this->username() => $pesan]);
    }

    /** Mengakhiri sesi akun yang belum berhak memakai aplikasi. */
    private function keluarkan(Request $request): void
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
