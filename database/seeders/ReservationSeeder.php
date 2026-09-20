<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Reservasi fixture RSV-1..9 + RSV-H1..H6 (README §7.7).
 * Semua tanggal dihitung dinamis dari now() WIB: H = hari seed.
 */
class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $H = $now->copy()->startOfDay();

        $user = fn (string $email): User => User::where('email', $email)->firstOrFail();
        $fac = fn (string $name): Facility => Facility::where('name', $name)->firstOrFail();

        $budi = $user('budi@kampus.test');
        $citra = $user('citra@kampus.test');
        $dimas = $user('dimas@kampus.test');
        $petugas = $user('petugas@kampus.test');

        [$rsv7Start, $rsv7End] = $this->jamRsv7($now);

        $data = [
            // [kode, pemohon, fasilitas, tanggal, mulai, selesai, status, tujuan, ekstra]
            ['RSV-1', $budi, 'Lab Komputer 1', $H->copy()->addDay(), '09:00', '10:30', 'disetujui',
                'Praktikum pemrograman web kelas A.', []],
            ['RSV-2', $budi, 'R-101', $H->copy()->addDay(), '13:00', '14:00', 'menunggu',
                'Rapat panitia kegiatan himpunan.', []],
            ['RSV-3', $citra, 'Lapangan Basket', $H->copy()->addDay(), '10:00', '11:00', 'disetujui',
                'Kelas olahraga mahasiswa semester 1.', []],
            ['RSV-4', $dimas, 'R-102', $H->copy()->addDays(2), '10:00', '11:00', 'menunggu',
                'Diskusi kelompok penelitian staf.', []],
            ['RSV-5', $citra, 'R-102', $H->copy()->addDays(2), '10:30', '11:30', 'menunggu',
                'Bimbingan tugas akhir mahasiswa.', []],
            ['RSV-6', $dimas, 'Lab Komputer 1', $H->copy()->addDay(), '10:00', '11:00', 'menunggu',
                'Pelatihan aplikasi perkantoran.', []],
            ['RSV-7', $budi, 'Proyektor P-01', $H->copy(), $rsv7Start, $rsv7End, 'disetujui',
                'Presentasi tugas mata kuliah sore ini.', []],
            ['RSV-8', $budi, 'R-102', $H->copy()->subDay(), '13:00', '14:00', 'ditolak',
                'Latihan presentasi kelompok.', ['rejection_reason' => 'Ruangan dipakai kegiatan akademik terjadwal.']],
            ['RSV-9', $budi, 'Lapangan Basket', $H->copy()->subDay(), '15:00', '16:00', 'dibatalkan',
                'Latihan rutin UKM basket.', ['cancel_reason' => 'Cuaca hujan, latihan ditunda.', 'cancelled_by' => 'budi']],

            // Data historis untuk rekap okupansi (SRS-004).
            ['RSV-H1', $budi, 'R-101', $H->copy()->subDays(2), '08:00', '10:00', 'disetujui',
                'Kuliah pengganti Algoritma.', []],
            ['RSV-H2', $citra, 'Aula Utama', $H->copy()->subDays(5), '09:00', '12:00', 'disetujui',
                'Seminar nasional fakultas.', []],
            ['RSV-H3', $dimas, 'Lab Komputer 1', $H->copy()->subDays(8), '13:00', '15:00', 'disetujui',
                'Pelatihan sistem informasi staf.', []],
            ['RSV-H4', $budi, 'Lapangan Basket', $H->copy()->subDays(12), '16:00', '18:00', 'disetujui',
                'Turnamen basket antar jurusan.', []],
            ['RSV-H5', $citra, 'R-101', $H->copy()->subDays(16), '10:00', '11:30', 'disetujui',
                'Kuliah tamu program studi.', []],
            ['RSV-H6', $dimas, 'Aula Utama', $H->copy()->subDays(20), '13:00', '16:00', 'disetujui',
                'Rapat kerja unit kerja kampus.', []],
        ];

        foreach ($data as [$kode, $pemohon, $namaFasilitas, $tanggal, $mulai, $selesai, $status, $tujuan, $ekstra]) {
            $reservation = new Reservation;
            $reservation->fill([
                'facility_id' => $fac($namaFasilitas)->id,
                'reservation_date' => $tanggal->format('Y-m-d'),
                'start_time' => $mulai,
                'end_time' => $selesai,
                'purpose' => $tujuan,
            ]);

            // Kolom non-fillable diisi eksplisit (D-06).
            $reservation->user_id = $pemohon->id;
            $reservation->status = $status;
            $reservation->rejection_reason = $ekstra['rejection_reason'] ?? null;
            $reservation->cancel_reason = $ekstra['cancel_reason'] ?? null;

            // Waktu pencatatan mengikuti tanggal reservasi agar data historis realistis.
            $dibuat = $tanggal->isPast()
                ? $tanggal->copy()->subDay()->setTime(9, 0)
                : $now->copy()->subDays(1);

            if (in_array($status, ['disetujui', 'ditolak'], true)) {
                $reservation->processed_by = $petugas->id;
                $reservation->processed_at = $dibuat->copy()->addHours(2);
            }

            if ($status === 'dibatalkan') {
                $reservation->cancelled_by = $budi->id;
                $reservation->cancelled_at = $dibuat->copy()->addHours(3);
            }

            $reservation->created_at = $dibuat;
            $reservation->updated_at = $dibuat;
            $reservation->save();
        }
    }

    /**
     * RSV-7: mulai = kelipatan 30 menit terdekat setelah now()+60 menit, durasi 30 menit,
     * tidak melewati tengah malam. Fixture ini boleh berada di luar jam operasional.
     *
     * @return array{0: string, 1: string}
     */
    private function jamRsv7(Carbon $now): array
    {
        $mulai = $now->copy()->addMinutes(60)->addMinutes(30 - ($now->copy()->addMinutes(60)->minute % 30 ?: 30))
            ->second(0);

        // Jangan melewati tengah malam: paling lambat 23.00–23.30.
        if ($mulai->format('Y-m-d') !== $now->format('Y-m-d') || $mulai->format('H:i') > '23:00') {
            return ['23:00', '23:30'];
        }

        return [$mulai->format('H:i'), $mulai->copy()->addMinutes(30)->format('H:i')];
    }
}
