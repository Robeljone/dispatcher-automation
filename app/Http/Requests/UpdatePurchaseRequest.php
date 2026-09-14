<?php

namespace App\Http\Requests;

use App\Enums\PurchaseStatus;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $purchase = $this->route('purchase');

        return $this->user() !== null
            && $purchase instanceof Purchase
            && $purchase->status === PurchaseStatus::Pending;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', Rule::exists(Item::class, 'id')],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['required', 'integer', Rule::exists(Supplier::class, 'id')],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ];
    }
}
