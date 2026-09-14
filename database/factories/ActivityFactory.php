<?php

namespace Database\Factories;

use App\Enums\ActivityAction;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => ActivityAction::LoggedIn,
            'description' => 'Signed in to the panel.',
            'ip_address' => fake()->ipv4(),
        ];
    }
}
