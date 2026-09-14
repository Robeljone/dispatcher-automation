<?php

use App\Models\Driver;
use App\Models\MaintenanceOwn;
use App\Models\OnroadDetail;
use App\Models\User;
use App\Models\Vehicle;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the on-road maintenance index for an authenticated user', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['plate_no' => 'AA-5678']);
    $driver = Driver::factory()->create(['full_name' => 'Alem Tesfaye']);
    MaintenanceOwn::factory()->create([
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
    ]);

    $this->actingAs($user)
        ->get(route('maintenance.on-road.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Maintenance/OnRoad/Index')
            ->has('records.data', 1)
            ->where('records.data.0.plate_no', 'AA-5678')
            ->where('records.data.0.driver_name', 'Alem Tesfaye')
        );
});

it('creates an on-road maintenance record with work lines', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    $this->actingAs($user)
        ->post(route('maintenance.on-road.store'), [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'details' => [
                ['type' => 'Puncture', 'quantity' => 2, 'price' => '150.00'],
            ],
        ])
        ->assertRedirect(route('maintenance.on-road.index'));

    $record = MaintenanceOwn::query()->first();

    expect($record)
        ->vehicle_id->toBe($vehicle->id)
        ->driver_id->toBe($driver->id)
        ->total->toBe('300.00');

    $this->assertDatabaseHas('onroad_detail', [
        'parent_id' => (string) $record->id,
        'type' => 'Puncture',
        'quantity' => 2,
        'price' => 150.00,
        'total' => 300.00,
    ]);
});

it('rejects an on-road maintenance create request with missing fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('maintenance.on-road.create'))
        ->post(route('maintenance.on-road.store'), [])
        ->assertRedirect(route('maintenance.on-road.create'))
        ->assertSessionHasErrors(['vehicle_id', 'driver_id']);
});

it('updates an on-road maintenance record and replaces line items', function () {
    $user = User::factory()->create();
    $record = MaintenanceOwn::factory()->create();
    OnroadDetail::factory()->create([
        'parent_id' => (string) $record->id,
        'type' => 'Old work',
    ]);

    $this->actingAs($user)
        ->put(route('maintenance.on-road.update', $record), [
            'vehicle_id' => $record->vehicle_id,
            'driver_id' => $record->driver_id,
            'details' => [
                ['type' => 'Belt', 'quantity' => 1, 'price' => '80'],
            ],
        ])
        ->assertRedirect(route('maintenance.on-road.index'));

    expect($record->fresh())->total->toBe('80.00');

    $this->assertDatabaseHas('onroad_detail', [
        'parent_id' => (string) $record->id,
        'type' => 'Belt',
    ]);
    $this->assertDatabaseMissing('onroad_detail', [
        'parent_id' => (string) $record->id,
        'type' => 'Old work',
    ]);
});

it('deletes an on-road maintenance record', function () {
    $user = User::factory()->create();
    $record = MaintenanceOwn::factory()->create();
    OnroadDetail::factory()->create(['parent_id' => (string) $record->id]);

    $this->actingAs($user)
        ->delete(route('maintenance.on-road.destroy', $record))
        ->assertRedirect(route('maintenance.on-road.index'));

    $this->assertModelMissing($record);
    $this->assertDatabaseMissing('onroad_detail', [
        'parent_id' => (string) $record->id,
    ]);
});
