<?php

namespace App\Models;

use Database\Factories\MaintenanceDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $type
 * @property int $quantity
 * @property string $price
 * @property string $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'parent_id',
    'type',
    'quantity',
    'price',
    'total',
])]
class MaintenanceDetail extends Model
{
    /** @use HasFactory<MaintenanceDetailFactory> */
    use HasFactory;

    protected $table = 'maintenance_detail';

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
