<?php

namespace Database\Factories;

use App\Enums\ItemRequestStatus;
use App\Models\Driver;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemRequest>
 */
class ItemRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 8);
        $price = fake()->randomFloat(2, 10, 200);

        return [
            'request_by' => Driver::factory(),
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'date_requested' => now()->toDateString(),
            'vehicle_id' => Vehicle::factory(),
            'total' => round($quantity * $price, 2),
            'status' => ItemRequestStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => ItemRequestStatus::Approved,
        ]);
    }
}
