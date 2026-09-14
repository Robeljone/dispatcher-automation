<?php

use App\Enums\PurchaseStatus;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the purchase index for an authenticated user', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Brake pad']);
    Purchase::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)
        ->get(route('inventory.purchases.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Purchases/Index')
            ->has('purchases.data', 1)
            ->where('purchases.data.0.item_name', 'Brake pad')
        );
});

it('creates a purchase for an existing supplier', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $supplier = Supplier::factory()->create();

    $this->actingAs($user)
        ->post(route('inventory.purchases.store'), [
            'item_id' => $item->id,
            'supplier_id' => $supplier->id,
            'quantity' => 6,
            'price' => '40.00',
        ])
        ->assertRedirect(route('inventory.purchases.index'));

    $this->assertDatabaseHas('purchases', [
        'item_id' => $item->id,
        'supplier_id' => $supplier->id,
        'quantity' => 6,
        'status' => PurchaseStatus::Pending->value,
        'approved' => false,
    ]);
});

it('rejects a purchase create request with missing fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('inventory.purchases.create'))
        ->post(route('inventory.purchases.store'), [])
        ->assertRedirect(route('inventory.purchases.create'))
        ->assertSessionHasErrors(['item_id', 'quantity', 'price', 'supplier_id']);
});

it('updates a pending purchase', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->create(['quantity' => 3]);

    $this->actingAs($user)
        ->put(route('inventory.purchases.update', $purchase), [
            'item_id' => $purchase->item_id,
            'supplier_id' => $purchase->supplier_id,
            'quantity' => 9,
            'price' => $purchase->price,
        ])
        ->assertRedirect(route('inventory.purchases.index'));

    expect($purchase->fresh()?->quantity)->toBe(9);
});

it('forbids updating an approved purchase', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->approved()->create();

    $this->actingAs($user)
        ->put(route('inventory.purchases.update', $purchase), [
            'item_id' => $purchase->item_id,
            'supplier_id' => $purchase->supplier_id,
            'quantity' => 1,
            'price' => $purchase->price,
        ])
        ->assertForbidden();
});

it('deletes a pending purchase', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->create();

    $this->actingAs($user)
        ->delete(route('inventory.purchases.destroy', $purchase))
        ->assertRedirect(route('inventory.purchases.index'));

    $this->assertModelMissing($purchase);
});
