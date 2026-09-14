<?php

use App\Models\User;

it('renders the profile page for an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Profile/Edit'));
});

it('updates the authenticated user profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Smooth Operator',
            'email' => 'smooth@example.com',
        ])
        ->assertRedirect();

    expect($user->fresh())
        ->name->toBe('Smooth Operator')
        ->email->toBe('smooth@example.com');
});

it('rejects a profile email that belongs to another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $other->email,
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('email');
});
