<?php

namespace Database\Factories;

use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'brand' => fake()->company(),
            'status' => fake()->randomElement(ItemStatus::cases()),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => ItemStatus::Inactive,
        ]);
    }
}
