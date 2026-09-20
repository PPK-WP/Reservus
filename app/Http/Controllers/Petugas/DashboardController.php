<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;

/**
 * Shell dashboard petugas. Isi antrian disuplai partial milik SRS-006 & SRS-008
 * (dipanggil dengan pemeriksaan keberadaan view agar baseline tetap jalan sendiri).
 */
class DashboardController extends Controller
{
    public function __invoke(): Renderable
    {
        return view('petugas.dashboard');
    }
}
