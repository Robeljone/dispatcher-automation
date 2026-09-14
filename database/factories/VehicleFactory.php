<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_type' => fake()->randomElement(['Truck', 'Trailer', 'Pickup']),
            'made_in' => fake()->country(),
            'model' => fake()->bothify('???-###'),
            'made_year' => fake()->numberBetween(1998, (int) now()->year),
            'chassis_no' => strtoupper(fake()->unique()->bothify('CHS########')),
            'motor_no' => strtoupper(fake()->bothify('MTR######')),
            'body_type' => fake()->randomElement(['Box', 'Flatbed', 'Tanker']),
            'color' => fake()->safeColorName(),
            'fuel_type' => fake()->randomElement(['Diesel', 'Petrol', 'Electric']),
            'motor_cc' => fake()->numberBetween(1500, 12000),
            'weight_total' => fake()->randomFloat(2, 2000, 40000),
            'weight_single' => fake()->randomFloat(2, 800, 8000),
            'load_amount' => fake()->randomFloat(2, 500, 25000),
            'motor_capacity' => (string) fake()->numberBetween(80, 500).' HP',
            'total_cylinder' => fake()->randomElement([4, 6, 8, 12]),
            'plate_no' => strtoupper(fake()->unique()->bothify('??-####')),
            'pre_plate_no' => fake()->optional()->bothify('??-####'),
        ];
    }
}
