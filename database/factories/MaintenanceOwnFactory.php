<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\MaintenanceOwn;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceOwn>
 */
class MaintenanceOwnFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'total' => 0,
        ];
    }
}
