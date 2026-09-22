<?php

// PEMILIK: P3 · SRS-008 Laporan Petugas · branch feature/laporan-petugas — HANYA pemilik yang boleh mengedit file ini.

use App\Http\Controllers\Petugas\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Pemrosesan Laporan Petugas (SRS-008 · US 8, 11, 12)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.reports.')
    ->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('index');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('show');
        Route::patch('/reports/{report}/status', [ReportController::class, 'status'])->name('status');
        Route::patch('/reports/{report}/facility-status', [ReportController::class, 'facilityStatus'])->name('facility-status');
    });
