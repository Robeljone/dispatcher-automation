<?php

use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('redirects guests from the stock index to login', function () {
    $this->get(route('inventory.stocks.index'))
        ->assertRedirect(route('login'));
});

it('renders the stock index for an authenticated user', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Oil filter']);
    Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 12]);

    $this->actingAs($user)
        ->get(route('inventory.stocks.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Stocks/Index')
            ->has('stocks.data', 1)
            ->where('stocks.data.0.item_name', 'Oil filter')
        );
});

it('creates stock when valid data is submitted', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Fuel pump']);

    $this->actingAs($user)
        ->post(route('inventory.stocks.store'), [
            'item_id' => $item->id,
            'stock_quantity' => 20,
            'min_quantity' => 4,
            'buy_price' => '15.50',
        ])
        ->assertRedirect(route('inventory.stocks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('stocks', [
        'item_id' => $item->id,
        'stock_quantity' => 20,
        'min_quantity' => 4,
        'buy_price' => 15.50,
    ]);
});

it('rejects a second stock row for the same item and buy price', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Stock::factory()->create(['item_id' => $item->id, 'buy_price' => '10.00']);

    $this->actingAs($user)
        ->from(route('inventory.stocks.create'))
        ->post(route('inventory.stocks.store'), [
            'item_id' => $item->id,
            'stock_quantity' => 5,
            'min_quantity' => 1,
            'buy_price' => '10',
        ])
        ->assertRedirect(route('inventory.stocks.create'))
        ->assertSessionHasErrors('buy_price');
});

it('creates a second stock row when the same item has a different buy price', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Stock::factory()->create(['item_id' => $item->id, 'buy_price' => '10.00', 'stock_quantity' => 4]);

    $this->actingAs($user)
        ->post(route('inventory.stocks.store'), [
            'item_id' => $item->id,
            'stock_quantity' => 5,
            'min_quantity' => 1,
            'buy_price' => '12.50',
        ])
        ->assertRedirect(route('inventory.stocks.index'));

    expect(Stock::query()->where('item_id', $item->id)->count())->toBe(2);
});

it('updates stock when valid data is submitted', function () {
    $user = User::factory()->create();
    $stock = Stock::factory()->create(['stock_quantity' => 8]);

    $this->actingAs($user)
        ->put(route('inventory.stocks.update', $stock), [
            'stock_quantity' => 18,
            'min_quantity' => 3,
            'buy_price' => '22.00',
        ])
        ->assertRedirect(route('inventory.stocks.index'));

    expect($stock->fresh()?->stock_quantity)->toBe(18);
});

it('deletes stock when requested', function () {
    $user = User::factory()->create();
    $stock = Stock::factory()->create();

    $this->actingAs($user)
        ->delete(route('inventory.stocks.destroy', $stock))
        ->assertRedirect(route('inventory.stocks.index'));

    $this->assertModelMissing($stock);
});
