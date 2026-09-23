<?php

// PEMILIK: P2 · SRS-005 Reservasi Pengguna · branch feature/reservasi-user — HANYA pemilik yang boleh mengedit file ini.

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:pengguna'])->group(function () {
	Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
	Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
	Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
	Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
	Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
});
