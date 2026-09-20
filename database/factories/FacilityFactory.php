<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Ruang '.fake()->unique()->numerify('R-###'),
            'type' => fake()->randomElement(array_keys(Facility::TYPES)),
            'location' => 'Gedung '.fake()->randomLetter(),
            'capacity' => fake()->numberBetween(10, 100),
            'description' => fake()->sentence(),
            'status' => 'aktif',
        ];
    }

    /** Fasilitas sedang diperbaiki (tidak bisa direservasi). */
    public function dalamPerbaikan(): static
    {
        return $this->state(fn (): array => ['status' => 'dalam_perbaikan']);
    }

    /** Fasilitas dinonaktifkan admin. */
    public function nonaktif(): static
    {
        return $this->state(fn (): array => ['status' => 'nonaktif']);
    }
}
