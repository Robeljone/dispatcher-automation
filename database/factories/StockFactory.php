<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'stock_quantity' => fake()->numberBetween(5, 80),
            'min_quantity' => fake()->numberBetween(1, 10),
            'buy_price' => fake()->randomFloat(2, 10, 500),
        ];
    }
}
