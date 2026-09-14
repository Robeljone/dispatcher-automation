<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@adoni.test'],
            [
                'name' => 'Adoni Admin',
                'password' => 'password',
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'user@adoni.test'],
            [
                'name' => 'Staff User',
                'password' => 'password',
                'role' => UserRole::User,
                'is_active' => true,
            ],
        );

        if (User::query()->count() < 8) {
            User::factory(6)->create();
        }

        User::query()->updateOrCreate(
            ['email' => 'inactive@adoni.test'],
            [
                'name' => 'Inactive Member',
                'password' => 'password',
                'role' => UserRole::User,
                'is_active' => false,
            ],
        );
    }
}
