<?php

namespace App\Actions;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Validation\ValidationException;

class AdjustStock
{
    public function handle(Item $item, int $delta, ?string $buyPrice = null): Stock
    {
        if ($delta > 0) {
            return $this->increase($item, $delta, $buyPrice);
        }

        return $this->decrease($item, abs($delta), $buyPrice);
    }

    private function increase(Item $item, int $quantity, ?string $buyPrice): Stock
    {
        $price = $this->normalizePrice($buyPrice ?? '0.00');

        $stock = Stock::query()
            ->where('item_id', $item->id)
            ->where('buy_price', $price)
            ->lockForUpdate()
            ->first();

        if ($stock === null) {
            $minQuantity = (int) (Stock::query()->where('item_id', $item->id)->value('min_quantity') ?? 0);

            return Stock::query()->create([
                'item_id' => $item->id,
                'stock_quantity' => $quantity,
                'min_quantity' => $minQuantity,
                'buy_price' => $price,
            ]);
        }

        $stock->stock_quantity += $quantity;
        $stock->save();

        return $stock;
    }

    private function decrease(Item $item, int $quantity, ?string $buyPrice): Stock
    {
        $lots = Stock::query()
            ->where('item_id', $item->id)
            ->when($buyPrice !== null, function ($query) use ($buyPrice): void {
                $query->where('buy_price', $this->normalizePrice($buyPrice));
            })
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($lots->sum('stock_quantity') < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'There is not enough stock for this item.',
            ]);
        }

        $remaining = $quantity;
        $lastTouched = $lots->first();

        foreach ($lots as $lot) {
            if ($remaining === 0) {
                break;
            }

            $take = min($lot->stock_quantity, $remaining);
            $lot->stock_quantity -= $take;
            $lot->save();
            $remaining -= $take;
            $lastTouched = $lot;
        }

        assert($lastTouched instanceof Stock);

        return $lastTouched;
    }

    private function normalizePrice(string $price): string
    {
        return number_format((float) $price, 2, '.', '');
    }
}
