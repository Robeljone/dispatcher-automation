<?php

use App\Enums\ItemRequestStatus;
use App\Enums\PurchaseStatus;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders pending purchases and item requests for approval', function () {
    $user = User::factory()->create();
    Purchase::factory()->create();
    Purchase::factory()->approved()->create();
    ItemRequest::factory()->create();
    ItemRequest::factory()->approved()->create();

    $this->actingAs($user)
        ->get(route('inventory.request-approvals'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/RequestApprovals/Index')
            ->has('purchases', 1)
            ->has('itemRequests', 1)
            ->where('tab', 'purchases')
        );
});

it('approves a purchase and adds quantity to matching stock when the price is the same', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 10, 'buy_price' => '9.25']);
    $purchase = Purchase::factory()->create([
        'item_id' => $item->id,
        'quantity' => 5,
        'price' => '9.25',
    ]);

    $this->actingAs($user)
        ->patch(route('inventory.request-approvals.purchases.update', $purchase), [
            'decision' => PurchaseStatus::Approved->value,
            'tab' => 'purchases',
        ])
        ->assertRedirect(route('inventory.request-approvals', ['tab' => 'purchases']));

    expect($purchase->fresh())
        ->status->toBe(PurchaseStatus::Approved)
        ->approved->toBeTrue();

    $lot = Stock::query()->where('item_id', $item->id)->first();

    expect(Stock::query()->where('item_id', $item->id)->count())->toBe(1);
    expect($lot)
        ->stock_quantity->toBe(15)
        ->buy_price->toBe('9.25');
});

it('approves a purchase into a new stock lot when the price differs', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 10, 'min_quantity' => 2, 'buy_price' => '8.00']);
    $purchase = Purchase::factory()->create([
        'item_id' => $item->id,
        'quantity' => 5,
        'price' => '9.25',
    ]);

    $this->actingAs($user)
        ->patch(route('inventory.request-approvals.purchases.update', $purchase), [
            'decision' => PurchaseStatus::Approved->value,
            'tab' => 'purchases',
        ])
        ->assertRedirect(route('inventory.request-approvals', ['tab' => 'purchases']));

    expect(Stock::query()->where('item_id', $item->id)->count())->toBe(2);
    $this->assertDatabaseHas('stocks', [
        'item_id' => $item->id,
        'stock_quantity' => 10,
        'buy_price' => 8.00,
    ]);
    $this->assertDatabaseHas('stocks', [
        'item_id' => $item->id,
        'stock_quantity' => 5,
        'buy_price' => 9.25,
        'min_quantity' => 2,
    ]);
});

it('rejects a purchase without changing stock', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $stock = Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 10]);
    $purchase = Purchase::factory()->create(['item_id' => $item->id, 'quantity' => 5]);

    $this->actingAs($user)
        ->patch(route('inventory.request-approvals.purchases.update', $purchase), [
            'decision' => PurchaseStatus::Rejected->value,
            'tab' => 'purchases',
        ])
        ->assertRedirect(route('inventory.request-approvals', ['tab' => 'purchases']));

    expect($purchase->fresh()?->status)->toBe(PurchaseStatus::Rejected);
    expect($stock->fresh()?->stock_quantity)->toBe(10);
});

it('approves an item request and deducts stock', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 10, 'buy_price' => '12.00']);
    $itemRequest = ItemRequest::factory()->create([
        'item_id' => $item->id,
        'quantity' => 4,
        'price' => '12.00',
        'total' => '48.00',
    ]);

    $this->actingAs($user)
        ->patch(route('inventory.request-approvals.item-requests.update', $itemRequest), [
            'decision' => ItemRequestStatus::Approved->value,
            'tab' => 'item-requests',
        ])
        ->assertRedirect(route('inventory.request-approvals', ['tab' => 'item-requests']));

    expect($itemRequest->fresh()?->status)->toBe(ItemRequestStatus::Approved);
    expect(Stock::query()->where('item_id', $item->id)->value('stock_quantity'))->toBe(6);
});

it('does not approve an item request when stock is insufficient', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Stock::factory()->create(['item_id' => $item->id, 'stock_quantity' => 1, 'buy_price' => '12.00']);
    $itemRequest = ItemRequest::factory()->create([
        'item_id' => $item->id,
        'quantity' => 4,
        'price' => '12.00',
        'total' => '48.00',
    ]);

    $this->actingAs($user)
        ->from(route('inventory.request-approvals', ['tab' => 'item-requests']))
        ->patch(route('inventory.request-approvals.item-requests.update', $itemRequest), [
            'decision' => ItemRequestStatus::Approved->value,
            'tab' => 'item-requests',
        ])
        ->assertRedirect(route('inventory.request-approvals', ['tab' => 'item-requests']))
        ->assertSessionHasErrors('quantity');

    expect($itemRequest->fresh()?->status)->toBe(ItemRequestStatus::Pending);
});

it('forbids approving a purchase that is not pending', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->approved()->create();

    $this->actingAs($user)
        ->patch(route('inventory.request-approvals.purchases.update', $purchase), [
            'decision' => PurchaseStatus::Approved->value,
        ])
        ->assertForbidden();
});
