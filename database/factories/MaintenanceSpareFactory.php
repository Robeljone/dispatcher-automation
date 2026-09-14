<?php

namespace Database\Factories;

use App\Models\MaintenanceGarage;
use App\Models\MaintenanceSpare;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceSpare>
 */
class MaintenanceSpareFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 20, 1500);
        $quantity = fake()->numberBetween(1, 4);

        return [
            'parent_id' => MaintenanceGarage::factory(),
            'name' => fake()->randomElement(['Oil filter', 'Brake pad', 'Spark plug', 'Belt']),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}
