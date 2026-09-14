<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
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
            'vehicle_type' => ['required', 'string', 'max:100'],
            'made_in' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'made_year' => ['required', 'integer', 'min:1950', 'max:'.((int) now()->year + 1)],
            'chassis_no' => ['required', 'string', 'max:100', Rule::unique('vehicles', 'chassis_no')],
            'motor_no' => ['required', 'string', 'max:100'],
            'body_type' => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'max:50'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'motor_cc' => ['nullable', 'integer', 'min:0'],
            'weight_total' => ['nullable', 'numeric', 'min:0'],
            'weight_single' => ['nullable', 'numeric', 'min:0'],
            'load_amount' => ['nullable', 'numeric', 'min:0'],
            'motor_capacity' => ['nullable', 'string', 'max:100'],
            'total_cylinder' => ['nullable', 'integer', 'min:0'],
            'plate_no' => ['required', 'string', 'max:50', Rule::unique('vehicles', 'plate_no')],
            'pre_plate_no' => ['nullable', 'string', 'max:50'],
        ];
    }
}
