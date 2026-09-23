<?php

// PEMILIK: P1 · SRS-003 Katalog & Ketersediaan Fasilitas · branch feature/fasilitas-katalog — HANYA pemilik yang boleh mengedit file ini.

use App\Http\Controllers\FacilityCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Katalog Publik (SRS-003)
|--------------------------------------------------------------------------
| Dua route ini TANPA auth: pengunjung boleh melihat katalog dan grid
| ketersediaan tanpa login (US-1 & US-2), tanpa memuat nama pemohon/tujuan.
*/

Route::get('/facilities', [FacilityCatalogController::class, 'index'])->name('facilities.index');

Route::get('/facilities/{facility}', [FacilityCatalogController::class, 'show'])->name('facilities.show');
