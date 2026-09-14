<?php

namespace App\Models;

use Database\Factories\MaintenanceSpareFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property int $quantity
 * @property string $price
 * @property string $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'parent_id',
    'name',
    'quantity',
    'price',
    'total',
])]
class MaintenanceSpare extends Model
{
    /** @use HasFactory<MaintenanceSpareFactory> */
    use HasFactory;

    protected $table = 'maintenance_spare';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<MaintenanceGarage, $this>
     */
    public function garage(): BelongsTo
    {
        return $this->belongsTo(MaintenanceGarage::class, 'parent_id');
    }
}
