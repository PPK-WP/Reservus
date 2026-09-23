<?php

// PEMILIK: P1 · SRS-004 Master Fasilitas & Rekap Admin · branch feature/fasilitas-admin — HANYA pemilik yang boleh mengedit file ini.
// Rute SRS-004: admin.facilities.*, admin.recap.index, admin.recap.export (README §7.3 §9.4).

use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\RecapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Master fasilitas (US-16).
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
    Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
    Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
    Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');

    // Toggle status aktif/dalam_perbaikan ↔ nonaktif (A10). PUT/PATCH tombol di halaman index.
    Route::patch('/facilities/{facility}/toggle', [FacilityController::class, 'toggle'])->name('facilities.toggle');

    // Rekap okupansi & laporan (US-17) + ekspor CSV/XLSX/PDF.
    Route::get('/recap', [RecapController::class, 'index'])->name('recap.index');
    Route::get('/recap/export', [RecapController::class, 'export'])->name('recap.export');
});