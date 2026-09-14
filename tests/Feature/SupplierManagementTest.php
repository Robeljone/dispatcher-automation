<?php

use App\Enums\ItemStatus;
use App\Models\Supplier;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the supplier index for an authenticated user', function () {
    $user = User::factory()->create();
    Supplier::factory()->create(['name' => 'Addis Parts']);

    $this->actingAs($user)
        ->get(route('inventory.suppliers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Suppliers/Index')
            ->has('suppliers.data', 1)
            ->where('suppliers.data.0.name', 'Addis Parts')
        );
});

it('creates a supplier when valid data is submitted', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('inventory.suppliers.store'), [
            'name' => 'Bole Motors',
            'address' => 'Bole',
            'phone' => '0911223344',
            'email' => 'bole@example.com',
            'status' => ItemStatus::Active->value,
        ])
        ->assertRedirect(route('inventory.suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'name' => 'Bole Motors',
        'email' => 'bole@example.com',
        'status' => ItemStatus::Active->value,
    ]);
});

it('rejects a supplier create request with missing fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('inventory.suppliers.create'))
        ->post(route('inventory.suppliers.store'), [])
        ->assertRedirect(route('inventory.suppliers.create'))
        ->assertSessionHasErrors(['name', 'address', 'phone', 'email', 'status']);
});

it('updates a supplier when valid data is submitted', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();

    $this->actingAs($user)
        ->put(route('inventory.suppliers.update', $supplier), [
            'name' => 'Updated Supplier',
            'address' => $supplier->address,
            'phone' => $supplier->phone,
            'email' => $supplier->email,
            'status' => ItemStatus::Inactive->value,
        ])
        ->assertRedirect(route('inventory.suppliers.index'));

    expect($supplier->fresh())
        ->name->toBe('Updated Supplier')
        ->status->toBe(ItemStatus::Inactive);
});

it('deletes a supplier when requested', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();

    $this->actingAs($user)
        ->delete(route('inventory.suppliers.destroy', $supplier))
        ->assertRedirect(route('inventory.suppliers.index'));

    $this->assertModelMissing($supplier);
});
