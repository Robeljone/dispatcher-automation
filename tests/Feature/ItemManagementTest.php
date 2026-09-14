<?php

use App\Enums\ItemStatus;
use App\Models\Item;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('redirects guests from the item index to login', function () {
    $this->get(route('inventory.items.index'))
        ->assertRedirect(route('login'));
});

it('renders the item index for an authenticated user', function () {
    $user = User::factory()->create();
    Item::factory()->create(['name' => 'Brake pad']);

    $this->actingAs($user)
        ->get(route('inventory.items.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Items/Index')
            ->has('items.data', 1)
            ->where('items.data.0.name', 'Brake pad')
        );
});

it('renders the item create page for an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.items.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Items/Create')
            ->has('statuses', 2)
        );
});

it('renders the item edit page for an authenticated user', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Brake pad']);

    $this->actingAs($user)
        ->get(route('inventory.items.edit', $item))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Items/Edit')
            ->where('item.name', 'Brake pad')
        );
});

it('filters items by name or brand', function () {
    $user = User::factory()->create();
    Item::factory()->create(['name' => 'Oil filter', 'brand' => 'Bosch']);
    Item::factory()->create(['name' => 'Air filter', 'brand' => 'Mann']);

    $this->actingAs($user)
        ->get(route('inventory.items.index', ['search' => 'Bosch']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('items.data', 1)
            ->where('items.data.0.name', 'Oil filter')
        );
});

it('creates an item when valid data is submitted', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('inventory.items.store'), [
            'name' => 'Fuel pump',
            'brand' => 'Delphi',
            'status' => ItemStatus::Active->value,
        ])
        ->assertRedirect(route('inventory.items.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('items', [
        'name' => 'Fuel pump',
        'brand' => 'Delphi',
        'status' => ItemStatus::Active->value,
    ]);
    $this->assertDatabaseHas('activities', [
        'user_id' => $user->id,
        'action' => 'item_created',
        'description' => 'Created item Fuel pump.',
    ]);
});

it('rejects an item create request with missing fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('inventory.items.create'))
        ->post(route('inventory.items.store'), [])
        ->assertRedirect(route('inventory.items.create'))
        ->assertSessionHasErrors(['name', 'brand', 'status']);
});

it('rejects an item create request with an invalid status', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('inventory.items.create'))
        ->post(route('inventory.items.store'), [
            'name' => 'Fuel pump',
            'brand' => 'Delphi',
            'status' => 'archived',
        ])
        ->assertRedirect(route('inventory.items.create'))
        ->assertSessionHasErrors('status');
});

it('updates an item when valid data is submitted', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->put(route('inventory.items.update', $item), [
            'name' => 'Updated filter',
            'brand' => $item->brand,
            'status' => ItemStatus::Inactive->value,
        ])
        ->assertRedirect(route('inventory.items.index'));

    expect($item->fresh())
        ->name->toBe('Updated filter')
        ->status->toBe(ItemStatus::Inactive);

    $this->assertDatabaseHas('activities', [
        'user_id' => $user->id,
        'action' => 'item_updated',
        'description' => 'Updated item Updated filter.',
    ]);
});

it('deletes an item when requested', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->delete(route('inventory.items.destroy', $item))
        ->assertRedirect(route('inventory.items.index'));

    $this->assertModelMissing($item);
    $this->assertDatabaseHas('activities', [
        'user_id' => $user->id,
        'action' => 'item_deleted',
        'description' => "Deleted item {$item->name}.",
    ]);
});
