<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** Peran akun: value ⇒ label Indonesia. */
    public const ROLES = [
        'admin' => 'Admin',
        'petugas' => 'Petugas',
        'pengguna' => 'Pengguna',
    ];

    /** Status akun: value ⇒ label Indonesia. */
    public const STATUSES = [
        'aktif' => 'Aktif',
        'pending' => 'Menunggu Verifikasi',
        'ditolak' => 'Ditolak',
    ];

    /** Jenis pengguna: value ⇒ label Indonesia. */
    public const USER_TYPES = [
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen',
        'staf' => 'Staf',
    ];

    /**
     * Kolom yang boleh diisi massal.
     * role, status, verification_note, verified_at TIDAK termasuk (D-06).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'identity_number',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isPengguna(): bool
    {
        return $this->role === 'pengguna';
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<Report, $this> */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
