<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'driver_license_no' => strtoupper(fake()->unique()->bothify('DL########')),
            'federal_no' => strtoupper(fake()->optional()->bothify('FED######')),
            'license_type' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'experience' => fake()->numberBetween(1, 25),
            'phone' => fake()->unique()->numerify('09########'),
            'address' => fake()->address(),
            'education' => fake()->randomElement(['High school', 'Diploma', 'Bachelor']),
        ];
    }
}
