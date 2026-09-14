<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('redirects guests from the user index to login', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

it('forbids a regular user from managing users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('renders the user index for an administrator', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Maya Stone']);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data', 2)
        );
});

it('creates a user when an administrator submits valid data', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::User->value,
            'is_active' => true,
        ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'jordan@example.com',
        'role' => UserRole::User->value,
        'is_active' => true,
    ]);
});

it('rejects a user create request with missing fields', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [])
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['name', 'email', 'password', 'role']);
});

it('updates a user when an administrator submits valid data', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => UserRole::User->value,
            'is_active' => false,
        ])
        ->assertRedirect(route('users.index'));

    expect($user->fresh())
        ->name->toBe('Updated Name')
        ->is_active->toBeFalse();
});

it('does not allow an administrator to delete themselves', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('users.destroy', $admin))
        ->assertForbidden();

    $this->assertModelExists($admin);
});

it('does not allow the last administrator to be demoted', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('users.edit', $admin))
        ->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => UserRole::User->value,
            'is_active' => true,
        ])
        ->assertRedirect(route('users.edit', $admin))
        ->assertSessionHasErrors('role');

    expect($admin->fresh()?->role)->toBe(UserRole::Admin);
});

it('deletes another user when requested by an administrator', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));

    $this->assertModelMissing($user);
});
