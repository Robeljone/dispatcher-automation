<?php

namespace App\Http\Requests;

use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockRequest extends FormRequest
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
        $stock = $this->route('stock');
        assert($stock instanceof Stock);

        return [
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'buy_price' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique(Stock::class, 'buy_price')
                    ->where('item_id', $stock->item_id)
                    ->ignore($stock),
            ],
        ];
    }
}
