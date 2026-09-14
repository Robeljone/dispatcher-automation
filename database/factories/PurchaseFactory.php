<?php

namespace Database\Factories;

use App\Enums\PurchaseStatus;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 20),
            'supplier_id' => Supplier::factory(),
            'price' => fake()->randomFloat(2, 10, 500),
            'attachment' => null,
            'status' => PurchaseStatus::Pending,
            'approved' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => PurchaseStatus::Approved,
            'approved' => true,
        ]);
    }
}
