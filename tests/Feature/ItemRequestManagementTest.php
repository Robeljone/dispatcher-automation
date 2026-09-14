<?php

use App\Enums\ItemRequestStatus;
use App\Models\Driver;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Stock;
use App\Models\User;
use App\Models\Vehicle;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the item request index for an authenticated user', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Air filter']);
    ItemRequest::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)
        ->get(route('inventory.item-requests.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/ItemRequests/Index')
            ->has('itemRequests.data', 1)
            ->where('itemRequests.data.0.item_name', 'Air filter')
        );
});

it('lists in-stock lots on the create form', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Air filter']);
    Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 12, 'buy_price' => '10.50']);
    Stock::factory()->create(['stock_quantity' => 0, 'buy_price' => '4.00']);

    $this->actingAs($user)
        ->get(route('inventory.item-requests.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/ItemRequests/Create')
            ->has('stocks', 1)
            ->where('stocks.0.item_name', 'Air filter')
            ->where('stocks.0.stock_quantity', 12)
            ->where('stocks.0.buy_price', '10.50')
        );
});

it('creates an item request from stock using the lot price for the total', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $stock = Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 10, 'buy_price' => '10.50']);
    $driver = Driver::factory()->create();
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($user)
        ->post(route('inventory.item-requests.store'), [
            'stock_id' => $stock->id,
            'request_by' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'quantity' => 3,
            'price' => '99.00',
            'date_requested' => '2026-09-14',
        ])
        ->assertRedirect(route('inventory.item-requests.index'));

    $this->assertDatabaseHas('item_requests', [
        'item_id' => $item->id,
        'request_by' => $driver->id,
        'quantity' => 3,
        'price' => 10.50,
        'total' => 31.50,
        'status' => ItemRequestStatus::Pending->value,
    ]);
});

it('rejects an item request create with missing fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('inventory.item-requests.create'))
        ->post(route('inventory.item-requests.store'), [])
        ->assertRedirect(route('inventory.item-requests.create'))
        ->assertSessionHasErrors(['stock_id', 'request_by', 'quantity', 'date_requested', 'vehicle_id'])
        ->assertSessionDoesntHaveErrors('price');
});

it('rejects an item request when the quantity exceeds available stock', function () {
    $user = User::factory()->create();
    $stock = Stock::factory()->create(['stock_quantity' => 2, 'buy_price' => '8.00']);
    $driver = Driver::factory()->create();
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($user)
        ->from(route('inventory.item-requests.create'))
        ->post(route('inventory.item-requests.store'), [
            'stock_id' => $stock->id,
            'request_by' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'quantity' => 5,
            'date_requested' => '2026-09-14',
        ])
        ->assertRedirect(route('inventory.item-requests.create'))
        ->assertSessionHasErrors('quantity');
});

it('updates a pending item request', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $stock = Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 10, 'buy_price' => '5.00']);
    $itemRequest = ItemRequest::factory()->create([
        'item_id' => $item->id,
        'quantity' => 2,
        'price' => '5.00',
        'total' => '10.00',
    ]);

    $this->actingAs($user)
        ->put(route('inventory.item-requests.update', $itemRequest), [
            'stock_id' => $stock->id,
            'request_by' => $itemRequest->request_by,
            'vehicle_id' => $itemRequest->vehicle_id,
            'quantity' => 4,
            'date_requested' => $itemRequest->date_requested->toDateString(),
        ])
        ->assertRedirect(route('inventory.item-requests.index'));

    expect($itemRequest->fresh())
        ->quantity->toBe(4)
        ->price->toBe('5.00')
        ->total->toBe('20.00');
});

it('forbids updating an approved item request', function () {
    $user = User::factory()->create();
    $itemRequest = ItemRequest::factory()->approved()->create();

    $this->actingAs($user)
        ->put(route('inventory.item-requests.update', $itemRequest), [
            'stock_id' => 1,
            'request_by' => $itemRequest->request_by,
            'vehicle_id' => $itemRequest->vehicle_id,
            'quantity' => 1,
            'date_requested' => $itemRequest->date_requested->toDateString(),
        ])
        ->assertForbidden();
});

it('deletes a pending item request', function () {
    $user = User::factory()->create();
    $itemRequest = ItemRequest::factory()->create();

    $this->actingAs($user)
        ->delete(route('inventory.item-requests.destroy', $itemRequest))
        ->assertRedirect(route('inventory.item-requests.index'));

    $this->assertModelMissing($itemRequest);
});
