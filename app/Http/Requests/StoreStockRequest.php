<?php

namespace App\Http\Requests;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', Rule::exists(Item::class, 'id')],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'buy_price' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique(Stock::class, 'buy_price')->where('item_id', $this->integer('item_id')),
            ],
        ];
    }
}
