<?php

namespace App\Http\Requests;

use App\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
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
        $driver = $this->route('driver');
        assert($driver instanceof Driver);

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'driver_license_no' => ['required', 'string', 'max:100', Rule::unique('drivers', 'driver_license_no')->ignore($driver)],
            'federal_no' => ['nullable', 'string', 'max:100'],
            'license_type' => ['required', 'string', 'max:50'],
            'experience' => ['nullable', 'integer', 'min:0'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
        ];
    }
}
