<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'facility_id' => Facility::factory(),
            'category' => fake()->randomElement(array_keys(Report::CATEGORIES)),
            'description' => fake()->sentence(),
            'photo' => null,
            'status' => 'baru',
        ];
    }
}
