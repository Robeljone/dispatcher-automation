<?php

use App\Models\Driver;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the driver index without a vehicle type column', function () {
    $user = User::factory()->create();
    Driver::factory()->create(['full_name' => 'Abebe Bekele']);

    $this->actingAs($user)
        ->get(route('operations.drivers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Operations/Drivers/Index')
            ->has('drivers.data', 1)
            ->where('drivers.data.0.full_name', 'Abebe Bekele')
            ->missing('drivers.data.0.vehicle_type')
        );
});

it('creates a driver without a vehicle type', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('operations.drivers.store'), [
            'full_name' => 'Chala Lemma',
            'driver_license_no' => 'DL12345678',
            'federal_no' => 'FED123',
            'license_type' => 'B',
            'experience' => 4,
            'phone' => '0911223344',
            'address' => 'Addis Ababa',
            'education' => 'Diploma',
            'salary' => 12000,
        ])
        ->assertRedirect(route('operations.drivers.index'))
        ->assertSessionDoesntHaveErrors('vehicle_type');

    $this->assertDatabaseHas('drivers', [
        'full_name' => 'Chala Lemma',
        'driver_license_no' => 'DL12345678',
    ]);
});

it('rejects a driver create request with missing fields and does not require vehicle type', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('operations.drivers.create'))
        ->post(route('operations.drivers.store'), [])
        ->assertRedirect(route('operations.drivers.create'))
        ->assertSessionHasErrors(['full_name', 'driver_license_no', 'license_type', 'phone', 'address'])
        ->assertSessionDoesntHaveErrors('vehicle_type');
});

it('updates a driver without a vehicle type', function () {
    $user = User::factory()->create();
    $driver = Driver::factory()->create();

    $this->actingAs($user)
        ->put(route('operations.drivers.update', $driver), [
            'full_name' => 'Updated Driver',
            'driver_license_no' => $driver->driver_license_no,
            'federal_no' => $driver->federal_no,
            'license_type' => $driver->license_type,
            'experience' => $driver->experience,
            'phone' => $driver->phone,
            'address' => $driver->address,
            'education' => $driver->education,
            'salary' => 15000,
        ])
        ->assertRedirect(route('operations.drivers.index'))
        ->assertSessionDoesntHaveErrors('vehicle_type');

    expect($driver->fresh()->full_name)->toBe('Updated Driver');
});

it('deletes a driver when requested', function () {
    $user = User::factory()->create();
    $driver = Driver::factory()->create();

    $this->actingAs($user)
        ->delete(route('operations.drivers.destroy', $driver))
        ->assertRedirect(route('operations.drivers.index'));

    $this->assertModelMissing($driver);
});
