<?php

namespace App\Models;

use Database\Factories\MaintenanceOwnFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property int $driver_id
 * @property string $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'vehicle_id',
    'driver_id',
    'total',
])]
class MaintenanceOwn extends Model
{
    /** @use HasFactory<MaintenanceOwnFactory> */
    use HasFactory;

    protected $table = 'maintenance_own';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (MaintenanceOwn $record): void {
            $record->details()->delete();
        });
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return HasMany<OnroadDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(OnroadDetail::class, 'parent_id');
    }
}
