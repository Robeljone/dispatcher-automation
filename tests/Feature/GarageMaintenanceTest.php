<?php

use App\Models\MaintenanceDetail;
use App\Models\MaintenanceGarage;
use App\Models\MaintenanceSpare;
use App\Models\User;
use App\Models\Vehicle;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the garage maintenance index for an authenticated user', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['plate_no' => 'AA-1234']);
    MaintenanceGarage::factory()->create(['vehicle_id' => $vehicle->id, 'current_km' => 42000]);

    $this->actingAs($user)
        ->get(route('maintenance.garage.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Maintenance/Garage/Index')
            ->has('records.data', 1)
            ->where('records.data.0.plate_no', 'AA-1234')
            ->where('records.data.0.current_km', 42000)
        );
});

it('creates a garage maintenance record with work and spare lines', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($user)
        ->post(route('maintenance.garage.store'), [
            'vehicle_id' => $vehicle->id,
            'prev_main_date' => '2026-08-01',
            'prev_main_km' => 40000,
            'current_km' => 45000,
            'inspection_detail' => 'Routine service',
            'details' => [
                ['type' => 'Oil change', 'quantity' => 2, 'price' => '250.00'],
            ],
            'spares' => [
                ['name' => 'Oil filter', 'quantity' => 1, 'price' => '40.50'],
            ],
        ])
        ->assertRedirect(route('maintenance.garage.index'));

    $record = MaintenanceGarage::query()->first();

    expect($record)
        ->vehicle_id->toBe($vehicle->id)
        ->current_km->toBe(45000)
        ->inspection_detail->toBe('Routine service')
        ->total->toBe('540.50');

    $this->assertDatabaseHas('maintenance_detail', [
        'parent_id' => $record->id,
        'type' => 'Oil change',
        'quantity' => 2,
        'price' => 250.00,
        'total' => 500.00,
    ]);
    $this->assertDatabaseHas('maintenance_spare', [
        'parent_id' => $record->id,
        'name' => 'Oil filter',
        'quantity' => 1,
        'price' => 40.50,
        'total' => 40.50,
    ]);
});

it('rejects a garage maintenance create request with missing fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('maintenance.garage.create'))
        ->post(route('maintenance.garage.store'), [])
        ->assertRedirect(route('maintenance.garage.create'))
        ->assertSessionHasErrors(['vehicle_id', 'current_km']);
});

it('updates a garage maintenance record and replaces line items', function () {
    $user = User::factory()->create();
    $record = MaintenanceGarage::factory()->create(['current_km' => 10000]);
    MaintenanceDetail::factory()->create(['parent_id' => $record->id, 'type' => 'Old work']);
    MaintenanceSpare::factory()->create(['parent_id' => $record->id, 'name' => 'Old spare']);

    $this->actingAs($user)
        ->put(route('maintenance.garage.update', $record), [
            'vehicle_id' => $record->vehicle_id,
            'prev_main_date' => '2026-07-01',
            'prev_main_km' => 9000,
            'current_km' => 12000,
            'inspection_detail' => 'Updated notes',
            'details' => [
                ['type' => 'Brake service', 'quantity' => 1, 'price' => '100'],
            ],
            'spares' => [],
        ])
        ->assertRedirect(route('maintenance.garage.index'));

    expect($record->fresh())
        ->current_km->toBe(12000)
        ->inspection_detail->toBe('Updated notes')
        ->total->toBe('100.00');

    $this->assertDatabaseHas('maintenance_detail', [
        'parent_id' => $record->id,
        'type' => 'Brake service',
    ]);
    $this->assertDatabaseMissing('maintenance_detail', [
        'parent_id' => $record->id,
        'type' => 'Old work',
    ]);
    $this->assertDatabaseMissing('maintenance_spare', [
        'parent_id' => $record->id,
        'name' => 'Old spare',
    ]);
});

it('deletes a garage maintenance record', function () {
    $user = User::factory()->create();
    $record = MaintenanceGarage::factory()->create();
    MaintenanceDetail::factory()->create(['parent_id' => $record->id]);

    $this->actingAs($user)
        ->delete(route('maintenance.garage.destroy', $record))
        ->assertRedirect(route('maintenance.garage.index'));

    $this->assertModelMissing($record);
});
