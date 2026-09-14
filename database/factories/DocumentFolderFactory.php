<?php

namespace Database\Factories;

use App\Models\DocumentFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentFolder>
 */
class DocumentFolderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'user_id' => User::factory(),
            'name' => fake()->unique()->words(2, true),
        ];
    }

    public function childOf(DocumentFolder $folder): static
    {
        return $this->state(fn (array $attributes): array => [
            'parent_id' => $folder->id,
        ]);
    }
}
