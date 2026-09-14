<?php

namespace Database\Factories;

use App\Models\MaintenanceGarage;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceGarage>
 */
class MaintenanceGarageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $previousKm = fake()->numberBetween(10000, 80000);

        return [
            'vehicle_id' => Vehicle::factory(),
            'prev_main_date' => fake()->optional()->date(),
            'prev_main_km' => $previousKm,
            'current_km' => $previousKm + fake()->numberBetween(500, 8000),
            'inspection_detail' => fake()->optional()->sentence(),
            'total' => 0,
        ];
    }
}
