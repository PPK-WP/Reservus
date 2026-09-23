<?php

// PEMILIK: P2 · SRS-006 Reservasi Petugas · branch feature/reservasi-petugas — HANYA pemilik yang boleh mengedit file ini.

use App\Http\Controllers\Petugas\ReservationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:petugas'])
	->prefix('petugas')
	->name('petugas.reservations.')
	->group(function () {
		Route::get('/reservations', [ReservationController::class, 'index'])->name('index');
		Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('show');
		Route::patch('/reservations/{reservation}/approve', [ReservationController::class, 'approve'])->name('approve');
		Route::patch('/reservations/{reservation}/reject', [ReservationController::class, 'reject'])->name('reject');
		Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
	});
