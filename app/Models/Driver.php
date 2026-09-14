<?php

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $full_name
 * @property string $driver_license_no
 * @property string|null $federal_no
 * @property string $license_type
 * @property int|null $experience
 * @property string $phone
 * @property string $address
 * @property string|null $education
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'full_name',
    'driver_license_no',
    'federal_no',
    'license_type',
    'experience',
    'phone',
    'address',
    'education',
])]
class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience' => 'integer',
        ];
    }
}
