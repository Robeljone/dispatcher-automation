<?php

namespace Database\Factories;

use App\Enums\ItemStatus;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'phone' => fake()->numerify('09########'),
            'email' => fake()->unique()->companyEmail(),
            'status' => ItemStatus::Active,
        ];
    }
}
