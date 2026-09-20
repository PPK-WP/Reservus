<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Registrasi mandiri pengguna (US-13).
 *
 * Akun baru selalu berperan 'pengguna' dengan status 'pending' dan TIDAK login
 * otomatis; admin yang memutuskan aktif atau ditolak (aturan bisnis 9).
 */
class RegisterController extends Controller implements HasMiddleware
{
    use RegistersUsers;

    /**
     * Middleware controller (Laravel 11+ : base Controller tidak lagi punya method middleware()).
     *
     * @return array<int, \Illuminate\Routing\Controllers\Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('guest'),
        ];
    }

    /**
     * Validasi data registrasi.
     *
     * @param  array<string, mixed>  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', Rule::in(array_keys(User::USER_TYPES))],
            'identity_number' => ['nullable', 'string', 'max:30'],
        ]);
    }

    /**
     * Membuat akun baru. role dan status diisi eksplisit di sini, tidak pernah
     * dibaca dari request, agar penyisipan role=admin lewat form tidak berpengaruh (D-06).
     *
     * @param  array<string, mixed>  $data
     */
    protected function create(array $data): User
    {
        $user = new User;
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'identity_number' => $data['identity_number'] ?? null,
        ]);
        $user->role = 'pengguna';
        $user->status = 'pending';
        $user->save();

        return $user;
    }

    /**
     * Menimpa perilaku bawaan: pendaftar TIDAK langsung login, melainkan
     * dikembalikan ke halaman masuk dengan pesan menunggu verifikasi.
     */
    public function register(Request $request): RedirectResponse
    {
        // Hanya data yang lolos validasi yang dipakai, bukan seluruh isi request.
        $data = $this->validator($request->all())->validate();

        $this->create($data);

        return redirect('/login')->with(
            'success',
            'Pendaftaran berhasil. Akun Anda menunggu verifikasi admin.'
        );
    }
}
