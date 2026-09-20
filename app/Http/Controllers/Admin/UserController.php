<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan akun oleh admin (US-14).
 *
 * Satu-satunya jalur pembuatan akun petugas; peran 'admin' tidak pernah bisa
 * dibuat lewat form ini walaupun nilainya disisipkan ke request.
 */
class UserController extends Controller
{
    /** Peran yang boleh dipilih admin saat membuat akun. */
    private const PERAN_BOLEH_DIBUAT = ['petugas', 'pengguna'];

    /** Daftar akun dengan penyaring peran dan status. */
    public function index(Request $request): Renderable
    {
        $filterRole = $request->query('role');
        $filterStatus = $request->query('status');

        $users = User::query()
            ->when(
                in_array($filterRole, array_keys(User::ROLES), true),
                fn ($query) => $query->where('role', $filterRole)
            )
            ->when(
                in_array($filterStatus, array_keys(User::STATUSES), true),
                fn ($query) => $query->where('status', $filterStatus)
            )
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filterRole' => $filterRole,
            'filterStatus' => $filterStatus,
        ]);
    }

    /** Formulir pembuatan akun. */
    public function create(): Renderable
    {
        return view('admin.users.create', [
            'peranPilihan' => array_intersect_key(User::ROLES, array_flip(self::PERAN_BOLEH_DIBUAT)),
        ]);
    }

    /** Menyimpan akun baru berstatus aktif. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'admin' sengaja tidak termasuk: peran admin tidak dibuat lewat form.
            'role' => ['required', Rule::in(self::PERAN_BOLEH_DIBUAT)],
            'user_type' => [
                'nullable',
                'required_if:role,pengguna',
                Rule::in(array_keys(User::USER_TYPES)),
            ],
            'identity_number' => ['nullable', 'string', 'max:30'],
        ]);

        $user = new User;
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            // Petugas tidak memiliki jenis pengguna.
            'user_type' => $data['role'] === 'pengguna' ? $data['user_type'] : null,
            'identity_number' => $data['identity_number'] ?? null,
        ]);
        // Kolom di luar $fillable diisi eksplisit (D-06).
        $user->role = $data['role'];
        $user->status = 'aktif';
        $user->verified_at = Carbon::now('Asia/Jakarta');
        $user->save();

        return redirect('/admin/users')->with(
            'success',
            'Akun '.$user->name.' ('.User::ROLES[$user->role].') berhasil dibuat dan langsung aktif.'
        );
    }
}
