<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGarageMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'details' => $this->filledLines($this->input('details'), 'type'),
            'spares' => $this->filledLines($this->input('spares'), 'name'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', Rule::exists(Vehicle::class, 'id')],
            'prev_main_date' => ['nullable', 'date'],
            'prev_main_km' => ['nullable', 'integer', 'min:0'],
            'current_km' => ['required', 'integer', 'min:0'],
            'inspection_detail' => ['nullable', 'string'],
            'details' => ['nullable', 'array'],
            'details.*.type' => ['required', 'string', 'max:255'],
            'details.*.quantity' => ['required', 'integer', 'min:1'],
            'details.*.price' => ['required', 'numeric', 'min:0'],
            'spares' => ['nullable', 'array'],
            'spares.*.name' => ['required', 'string', 'max:255'],
            'spares.*.quantity' => ['required', 'integer', 'min:1'],
            'spares.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function filledLines(mixed $lines, string $labelKey): array
    {
        if (! is_array($lines)) {
            return [];
        }

        return collect($lines)
            ->filter(function (mixed $line) use ($labelKey): bool {
                if (! is_array($line)) {
                    return false;
                }

                return filled($line[$labelKey] ?? null) || filled($line['price'] ?? null);
            })
            ->values()
            ->all();
    }
}
