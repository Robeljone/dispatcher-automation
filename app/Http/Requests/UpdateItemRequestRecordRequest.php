<?php

namespace App\Http\Requests;

use App\Enums\ItemRequestStatus;
use App\Models\Driver;
use App\Models\ItemRequest;
use App\Models\Stock;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateItemRequestRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $itemRequest = $this->route('item_request');

        return $this->user() !== null
            && $itemRequest instanceof ItemRequest
            && $itemRequest->status === ItemRequestStatus::Pending;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'stock_id' => ['required', 'integer', Rule::exists(Stock::class, 'id')],
            'request_by' => ['required', 'integer', Rule::exists(Driver::class, 'id')],
            'quantity' => ['required', 'integer', 'min:1'],
            'date_requested' => ['required', 'date'],
            'vehicle_id' => ['required', 'integer', Rule::exists(Vehicle::class, 'id')],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['stock_id', 'quantity'])) {
                    return;
                }

                $stock = Stock::query()->find($this->integer('stock_id'));

                if ($stock === null) {
                    return;
                }

                if ($this->integer('quantity') > $stock->stock_quantity) {
                    $validator->errors()->add('quantity', 'There is not enough stock for this item.');
                }
            },
        ];
    }
}
