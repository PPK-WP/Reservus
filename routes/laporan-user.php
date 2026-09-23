<?php

// PEMILIK: P3 · SRS-007 Laporan Pengguna · branch feature/laporan-user — HANYA pemilik yang boleh mengedit file ini.

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Laporan Pengguna (SRS-007 · US 6, 7)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pengguna'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});
