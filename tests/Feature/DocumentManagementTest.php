<?php

use App\Models\DocumentFile;
use App\Models\DocumentFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('redirects guests from document uploads to login', function () {
    $this->post(route('documents.files.store'))
        ->assertRedirect(route('login'));
});

it('stores an uploaded pdf in the current folder', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $folder = DocumentFolder::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('policy.pdf', 120, 'application/pdf');

    $this->actingAs($user)
        ->post(route('documents.files.store'), [
            'folder_id' => $folder->id,
            'file' => $file,
        ])
        ->assertRedirect(route('documents.index', ['folder' => $folder->id]))
        ->assertSessionHas('success');

    $stored = DocumentFile::query()->first();

    expect($stored)
        ->not->toBeNull()
        ->folder_id->toBe($folder->id)
        ->name->toBe('policy.pdf')
        ->user_id->toBe($user->id);

    Storage::disk('local')->assertExists($stored->path);
});

it('finds folders and files by name and extension', function () {
    $user = User::factory()->create();
    $contracts = DocumentFolder::factory()->create([
        'user_id' => $user->id,
        'name' => 'Contracts',
    ]);
    DocumentFolder::factory()->create([
        'user_id' => $user->id,
        'name' => 'Payroll',
    ]);
    DocumentFile::factory()->create([
        'user_id' => $user->id,
        'folder_id' => $contracts->id,
        'name' => 'lease.pdf',
        'mime_type' => 'application/pdf',
    ]);
    DocumentFile::factory()->create([
        'user_id' => $user->id,
        'folder_id' => null,
        'name' => 'badge.png',
        'mime_type' => 'image/png',
    ]);

    $this->actingAs($user)
        ->get(route('documents.index', ['search' => 'pdf']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Documents/Index')
            ->where('search', 'pdf')
            ->has('items', 1)
            ->where('items.0.name', 'lease.pdf')
            ->where('items.0.location', 'Company Documents \\ Contracts')
        );

    $this->actingAs($user)
        ->get(route('documents.index', ['search' => '.png']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('items', 1)
            ->where('items.0.name', 'badge.png')
        );

    $this->actingAs($user)
        ->get(route('documents.index', ['search' => 'Contracts']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('items', 1)
            ->where('items.0.name', 'Contracts')
            ->where('items.0.kind', 'folder')
        );
});

it('rejects a file type that is not allowed', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('documents.index'))
        ->post(route('documents.files.store'), [
            'file' => UploadedFile::fake()->create('notes.txt', 20, 'text/plain'),
        ])
        ->assertRedirect(route('documents.index'))
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('document_files', 0);
});
