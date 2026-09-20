<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Laporan fixture LPR-1..6 + LPR-H1..H3 (README §7.7).
 */
class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $user = fn (string $email): User => User::where('email', $email)->firstOrFail();
        $fac = fn (string $name): Facility => Facility::where('name', $name)->firstOrFail();

        $budi = $user('budi@kampus.test');
        $citra = $user('citra@kampus.test');
        $dimas = $user('dimas@kampus.test');
        $petugas = $user('petugas@kampus.test');

        $data = [
            // [kode, pelapor, fasilitas, kategori, deskripsi, status, catatan resolusi, hari lalu]
            ['LPR-1', $budi, 'Lab Bahasa', 'kerusakan', 'AC ruangan mati sejak pagi, ruangan panas.', 'diproses', null, 2],
            ['LPR-2', $dimas, 'Aula Utama', 'kerusakan', 'Sound system tidak mengeluarkan suara.', 'baru', null, 1],
            ['LPR-3', $citra, 'R-102', 'kebersihan', 'Sampah menumpuk di sudut ruangan.', 'baru', null, 1],
            ['LPR-4', $budi, 'Proyektor P-01', 'peralatan', 'Kabel HDMI putus sehingga tidak bisa dipakai.', 'baru', null, 0],
            ['LPR-5', $budi, 'R-101', 'kerusakan', 'Dua kursi patah pada bagian sandaran.', 'selesai',
                'Kursi sudah diganti dengan unit baru.', 7],
            ['LPR-6', $citra, 'Lapangan Futsal', 'kebersihan', 'Rumput liar tumbuh di pinggir lapangan.', 'ditolak',
                'Lapangan berstatus nonaktif dan sedang direnovasi total.', 9],

            // Data historis untuk rekap frekuensi laporan (SRS-004).
            ['LPR-H1', $budi, 'Lab Komputer 1', 'peralatan', 'Mouse pada PC nomor 12 tidak berfungsi.', 'selesai',
                'Mouse sudah diganti.', 5],
            ['LPR-H2', $dimas, 'Aula Utama', 'kebersihan', 'Lantai aula kotor setelah acara.', 'selesai',
                'Sudah dibersihkan petugas kebersihan.', 15],
            ['LPR-H3', $citra, 'R-101', 'kerusakan', 'Lampu depan kelas mati.', 'selesai',
                'Lampu sudah diganti.', 25],
        ];

        foreach ($data as [$kode, $pelapor, $namaFasilitas, $kategori, $deskripsi, $status, $catatan, $hariLalu]) {
            $report = new Report;
            $report->fill([
                'facility_id' => $fac($namaFasilitas)->id,
                'category' => $kategori,
                'description' => $deskripsi,
                'photo' => null,
            ]);

            // Kolom non-fillable diisi eksplisit (D-06).
            $report->user_id = $pelapor->id;
            $report->status = $status;
            $report->resolution_note = $catatan;

            $dibuat = $now->copy()->subDays($hariLalu)->setTime(8, 30);

            if ($status !== 'baru') {
                $report->processed_by = $petugas->id;
                $report->processed_at = $dibuat->copy()->addHours(5);
            }

            $report->created_at = $dibuat;
            $report->updated_at = $dibuat;
            $report->save();
        }
    }
}
