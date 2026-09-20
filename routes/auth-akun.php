<?php

// PEMILIK: PM · SRS-002 Autentikasi & Manajemen Akun · branch feature/auth-akun — HANYA pemilik yang boleh mengedit file ini.

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Daftar & pembuatan akun oleh admin (US-14)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        // Verifikasi pendaftaran mandiri (US-15)
        Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
        Route::patch('/verifications/{user}/approve', [VerificationController::class, 'approve'])->name('verifications.approve');
        Route::patch('/verifications/{user}/reject', [VerificationController::class, 'reject'])->name('verifications.reject');
    });
