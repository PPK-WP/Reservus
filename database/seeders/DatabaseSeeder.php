<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Fixture bersama seluruh SRS (README §7.7) — jangan diubah oleh branch SRS.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            FacilitySeeder::class,
            ReservationSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
