<?php

namespace Database\Factories;

use App\Models\MaintenanceOwn;
use App\Models\OnroadDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnroadDetail>
 */
class OnroadDetailFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 2500);
        $quantity = fake()->numberBetween(1, 4);

        return [
            'parent_id' => MaintenanceOwn::factory()->create()->id,
            'type' => fake()->randomElement(['Puncture', 'Belt', 'Jump start', 'Tow']),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}
