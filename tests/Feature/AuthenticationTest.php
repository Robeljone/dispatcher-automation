<?php

use App\Models\User;

it('renders the login page for guests', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Auth/Login'));
});

it('authenticates a user with valid credentials', function () {
    $user = User::factory()->create();

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
    $this->assertNotNull($user->fresh()?->last_login_at);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects an inactive account', function () {
    $user = User::factory()->inactive()->create();

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('redirects authenticated users away from login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

it('logs an authenticated user out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('redirects guests from the dashboard to login', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});
