<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Petugas\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Bersama (SRS-001 — PEMILIK: PM)
|--------------------------------------------------------------------------
| File ini HANYA berisi route baseline + require satu file route per SRS (D-03).
| Anggota tim menulis route HANYA di file route milik SRS-nya, jangan di sini.
*/

Route::redirect('/', '/facilities');

// Reset password, verifikasi email, dan konfirmasi password dimatikan (D-07).
Auth::routes(['reset' => false, 'verify' => false, 'confirm' => false]);

Route::get('/home', HomeController::class)
    ->middleware('auth')
    ->name('home');

Route::get('/petugas', DashboardController::class)
    ->middleware(['auth', 'role:petugas'])
    ->name('petugas.dashboard');

require __DIR__.'/auth-akun.php';          // SRS-002 · PM
require __DIR__.'/fasilitas-katalog.php';  // SRS-003 · P1
require __DIR__.'/fasilitas-admin.php';    // SRS-004 · P1
require __DIR__.'/reservasi-user.php';     // SRS-005 · P2
require __DIR__.'/reservasi-petugas.php';  // SRS-006 · P2
require __DIR__.'/laporan-user.php';       // SRS-007 · P3
require __DIR__.'/laporan-petugas.php';    // SRS-008 · P3
