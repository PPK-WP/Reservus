<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

/**
 * 8 fasilitas fixture README §7.7.
 */
class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $fasilitas = [
            ['R-101', 'ruang_kelas', 'Gedung A', 40, 'Ruang kelas standar dengan proyektor.', 'aktif'],
            ['R-102', 'ruang_kelas', 'Gedung A', 30, 'Ruang kelas kecil untuk diskusi.', 'aktif'],
            ['Aula Utama', 'aula', 'Gedung Rektorat', 300, 'Aula untuk seminar dan acara besar.', 'aktif'],
            ['Lab Komputer 1', 'laboratorium', 'Gedung B', 40, 'Laboratorium komputer 40 unit PC.', 'aktif'],
            ['Lab Bahasa', 'laboratorium', 'Gedung B', 25, 'Laboratorium bahasa dengan headset.', 'dalam_perbaikan'],
            ['Proyektor P-01', 'alat', 'Gedung A', 1, 'Proyektor portabel untuk dipinjam.', 'aktif'],
            ['Lapangan Basket', 'lapangan', 'Area Olahraga', 20, 'Lapangan basket outdoor.', 'aktif'],
            ['Lapangan Futsal', 'lapangan', 'Area Olahraga', 14, 'Lapangan futsal outdoor.', 'nonaktif'],
        ];

        foreach ($fasilitas as [$name, $type, $location, $capacity, $description, $status]) {
            $facility = new Facility;
            $facility->fill([
                'name' => $name,
                'type' => $type,
                'location' => $location,
                'capacity' => $capacity,
                'description' => $description,
            ]);
            $facility->status = $status; // non-fillable (D-06)
            $facility->save();
        }
    }
}
