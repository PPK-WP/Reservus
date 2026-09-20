<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Akun fixture README §7.7 — password semua akun: 'password'.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $akun = [
            [
                'name' => 'Admin Kampus',
                'email' => 'admin@kampus.test',
                'role' => 'admin',
                'status' => 'aktif',
                'user_type' => null,
                'identity_number' => null,
                'verification_note' => null,
                'verified_at' => $now->copy()->subDays(30),
            ],
            [
                'name' => 'Petugas Fasilitas',
                'email' => 'petugas@kampus.test',
                'role' => 'petugas',
                'status' => 'aktif',
                'user_type' => null,
                'identity_number' => null,
                'verification_note' => null,
                'verified_at' => $now->copy()->subDays(30),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@kampus.test',
                'role' => 'pengguna',
                'status' => 'aktif',
                'user_type' => 'mahasiswa',
                'identity_number' => '2021010001',
                'verification_note' => null,
                'verified_at' => $now->copy()->subDays(25),
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra@kampus.test',
                'role' => 'pengguna',
                'status' => 'aktif',
                'user_type' => 'dosen',
                'identity_number' => '198503122010012001',
                'verification_note' => null,
                'verified_at' => $now->copy()->subDays(25),
            ],
            [
                'name' => 'Dimas Prakoso',
                'email' => 'dimas@kampus.test',
                'role' => 'pengguna',
                'status' => 'aktif',
                'user_type' => 'staf',
                'identity_number' => '199001152015031002',
                'verification_note' => null,
                'verified_at' => $now->copy()->subDays(20),
            ],
            [
                'name' => 'Pending Mahasiswa',
                'email' => 'pending@kampus.test',
                'role' => 'pengguna',
                'status' => 'pending',
                'user_type' => 'mahasiswa',
                'identity_number' => '2023010099',
                'verification_note' => null,
                'verified_at' => null,
            ],
            [
                'name' => 'Ditolak Mahasiswa',
                'email' => 'ditolak@kampus.test',
                'role' => 'pengguna',
                'status' => 'ditolak',
                'user_type' => 'mahasiswa',
                'identity_number' => '2023010100',
                'verification_note' => 'NIM tidak terdaftar pada data akademik.',
                'verified_at' => $now->copy()->subDays(3),
            ],
        ];

        foreach ($akun as $data) {
            $user = new User;
            // Kolom fillable.
            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => 'password',
                'user_type' => $data['user_type'],
                'identity_number' => $data['identity_number'],
            ]);
            // Kolom non-fillable diisi eksplisit (D-06).
            $user->role = $data['role'];
            $user->status = $data['status'];
            $user->verification_note = $data['verification_note'];
            $user->verified_at = $data['verified_at'];
            $user->save();
        }
    }
}
