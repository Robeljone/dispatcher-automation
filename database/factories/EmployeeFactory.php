<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'phone' => fake()->unique()->numerify('09########'),
            'address' => fake()->address(),
            'education' => fake()->randomElement(['Diploma', 'Bachelor', 'Master']),
            'position' => fake()->jobTitle(),
            'salary' => fake()->randomFloat(2, 8000, 45000),
        ];
    }
}
