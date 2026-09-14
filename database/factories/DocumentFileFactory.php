<?php

namespace Database\Factories;

use App\Models\DocumentFile;
use App\Models\DocumentFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentFile>
 */
class DocumentFileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word().'.pdf';

        return [
            'folder_id' => null,
            'user_id' => User::factory(),
            'name' => $name,
            'path' => 'company-documents/'.Str::uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
        ];
    }

    public function inFolder(DocumentFolder $folder): static
    {
        return $this->state(fn (array $attributes): array => [
            'folder_id' => $folder->id,
        ]);
    }
}
