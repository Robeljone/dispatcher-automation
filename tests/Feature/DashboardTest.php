<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the dashboard for an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('stats')
            ->has('recentActivities')
        );
});
