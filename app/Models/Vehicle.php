<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $vehicle_type
 * @property string $made_in
 * @property string $model
 * @property int $made_year
 * @property string $chassis_no
 * @property string $motor_no
 * @property string $body_type
 * @property string $color
 * @property string $fuel_type
 * @property int|null $motor_cc
 * @property string|null $weight_total
 * @property string|null $weight_single
 * @property string|null $load_amount
 * @property string|null $motor_capacity
 * @property int|null $total_cylinder
 * @property string $plate_no
 * @property string|null $pre_plate_no
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'vehicle_type',
    'made_in',
    'model',
    'made_year',
    'chassis_no',
    'motor_no',
    'body_type',
    'color',
    'fuel_type',
    'motor_cc',
    'weight_total',
    'weight_single',
    'load_amount',
    'motor_capacity',
    'total_cylinder',
    'plate_no',
    'pre_plate_no',
])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'made_year' => 'integer',
            'motor_cc' => 'integer',
            'weight_total' => 'decimal:2',
            'weight_single' => 'decimal:2',
            'load_amount' => 'decimal:2',
            'total_cylinder' => 'integer',
        ];
    }

    /**
     * @return HasMany<MaintenanceGarage, $this>
     */
    public function garageMaintenances(): HasMany
    {
        return $this->hasMany(MaintenanceGarage::class);
    }

    /**
     * @return HasMany<MaintenanceOwn, $this>
     */
    public function onRoadMaintenances(): HasMany
    {
        return $this->hasMany(MaintenanceOwn::class);
    }
}
