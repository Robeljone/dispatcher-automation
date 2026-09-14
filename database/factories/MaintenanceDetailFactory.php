<?php

namespace Database\Factories;

use App\Models\MaintenanceDetail;
use App\Models\MaintenanceGarage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceDetail>
 */
class MaintenanceDetailFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 2500);
        $quantity = fake()->numberBetween(1, 4);

        return [
            'parent_id' => MaintenanceGarage::factory(),
            'type' => fake()->randomElement(['Oil change', 'Brake service', 'Alignment', 'Inspection']),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}
