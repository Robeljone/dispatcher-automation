<?php

namespace App\Http\Requests;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOnRoadMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'details' => $this->filledLines($this->input('details')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', Rule::exists(Vehicle::class, 'id')],
            'driver_id' => ['required', 'integer', Rule::exists(Driver::class, 'id')],
            'details' => ['nullable', 'array'],
            'details.*.type' => ['required', 'string', 'max:255'],
            'details.*.quantity' => ['required', 'integer', 'min:1'],
            'details.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function filledLines(mixed $lines): array
    {
        if (! is_array($lines)) {
            return [];
        }

        return collect($lines)
            ->filter(function (mixed $line): bool {
                if (! is_array($line)) {
                    return false;
                }

                return filled($line['type'] ?? null) || filled($line['price'] ?? null);
            })
            ->values()
            ->all();
    }
}
