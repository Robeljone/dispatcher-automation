<?php

namespace App\Models;

use App\Enums\ItemRequestStatus;
use Database\Factories\ItemRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $request_by
 * @property int $item_id
 * @property int $quantity
 * @property string $price
 * @property Carbon $date_requested
 * @property int $vehicle_id
 * @property string $total
 * @property ItemRequestStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'request_by',
    'item_id',
    'quantity',
    'price',
    'date_requested',
    'vehicle_id',
    'total',
    'status',
])]
class ItemRequest extends Model
{
    /** @use HasFactory<ItemRequestFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'date_requested' => 'date',
            'total' => 'decimal:2',
            'status' => ItemRequestStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'request_by');
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
