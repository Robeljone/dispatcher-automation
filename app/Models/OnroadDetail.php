<?php

namespace App\Models;

use Database\Factories\OnroadDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $parent_id
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
class OnroadDetail extends Model
{
    /** @use HasFactory<OnroadDetailFactory> */
    use HasFactory;

    protected $table = 'onroad_detail';

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
     * @return BelongsTo<MaintenanceOwn, $this>
     */
    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOwn::class, 'parent_id');
    }
}
