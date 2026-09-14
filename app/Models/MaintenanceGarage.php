<?php

namespace App\Models;

use Database\Factories\MaintenanceGarageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property Carbon|null $prev_main_date
 * @property int|null $prev_main_km
 * @property int $current_km
 * @property string|null $inspection_detail
 * @property string $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'vehicle_id',
    'prev_main_date',
    'prev_main_km',
    'current_km',
    'inspection_detail',
    'total',
])]
class MaintenanceGarage extends Model
{
    /** @use HasFactory<MaintenanceGarageFactory> */
    use HasFactory;

    protected $table = 'maintenance_garage';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prev_main_date' => 'date',
            'prev_main_km' => 'integer',
            'current_km' => 'integer',
            'total' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return HasMany<MaintenanceDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(MaintenanceDetail::class, 'parent_id');
    }

    /**
     * @return HasMany<MaintenanceSpare, $this>
     */
    public function spares(): HasMany
    {
        return $this->hasMany(MaintenanceSpare::class, 'parent_id');
    }
}
