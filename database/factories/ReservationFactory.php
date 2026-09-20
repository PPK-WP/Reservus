<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'facility_id' => Facility::factory(),
            'reservation_date' => now('Asia/Jakarta')->addDay()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'purpose' => fake()->sentence(),
            'status' => 'menunggu',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => ['status' => 'disetujui']);
    }
}
