<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Enums\ItemRequestStatus;
use App\Http\Requests\StoreItemRequestRecordRequest;
use App\Http\Requests\UpdateItemRequestRecordRequest;
use App\Models\Driver;
use App\Models\ItemRequest;
use App\Models\Stock;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $itemRequests = ItemRequest::query()
            ->with(['item', 'vehicle', 'requester'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('item', function ($query) use ($search): void {
                        $query->where('name', 'like', '%'.$search.'%');
                    })->orWhereHas('vehicle', function ($query) use ($search): void {
                        $query->where('plate_no', 'like', '%'.$search.'%');
                    })->orWhereHas('requester', function ($query) use ($search): void {
                        $query->where('full_name', 'like', '%'.$search.'%');
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ItemRequest $itemRequest): array => [
                'id' => $itemRequest->id,
                'item_name' => $itemRequest->item->name,
                'quantity' => $itemRequest->quantity,
                'price' => $itemRequest->price,
                'total' => $itemRequest->total,
                'vehicle' => $itemRequest->vehicle->plate_no,
                'driver' => $itemRequest->requester->full_name,
                'date_requested' => $itemRequest->date_requested->toDateString(),
                'status' => $itemRequest->status->value,
                'status_label' => $itemRequest->status->label(),
            ]);

        return Inertia::render('Inventory/ItemRequests/Index', [
            'itemRequests' => $itemRequests,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Inventory/ItemRequests/Create', [
            'stocks' => $this->stockOptions(),
            'vehicles' => $this->vehicleOptions(),
            'drivers' => $this->driverOptions(),
        ]);
    }

    public function store(StoreItemRequestRecordRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $stock = Stock::query()->findOrFail($request->integer('stock_id'));
        $quantity = $request->integer('quantity');
        $price = (float) $stock->buy_price;

        $itemRequest = ItemRequest::query()->create([
            'request_by' => $request->integer('request_by'),
            'item_id' => $stock->item_id,
            'quantity' => $quantity,
            'price' => $price,
            'date_requested' => $request->date('date_requested'),
            'vehicle_id' => $request->integer('vehicle_id'),
            'total' => round($quantity * $price, 2),
            'status' => ItemRequestStatus::Pending,
        ]);
        $itemRequest->load('item');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::ItemRequestCreated, "Created item request for {$itemRequest->item->name}.");

        return redirect()->route('inventory.item-requests.index')->with('success', 'Item request created.');
    }

    public function edit(ItemRequest $itemRequest): Response
    {
        return Inertia::render('Inventory/ItemRequests/Edit', [
            'itemRequest' => [
                'id' => $itemRequest->id,
                'stock_id' => $this->stockIdFor($itemRequest),
                'request_by' => $itemRequest->request_by,
                'quantity' => $itemRequest->quantity,
                'date_requested' => $itemRequest->date_requested->toDateString(),
                'vehicle_id' => $itemRequest->vehicle_id,
                'can_edit' => $itemRequest->status === ItemRequestStatus::Pending,
            ],
            'stocks' => $this->stockOptions($itemRequest),
            'vehicles' => $this->vehicleOptions(),
            'drivers' => $this->driverOptions(),
        ]);
    }

    public function update(UpdateItemRequestRecordRequest $request, ItemRequest $itemRequest, RecordActivity $recordActivity): RedirectResponse
    {
        $stock = Stock::query()->findOrFail($request->integer('stock_id'));
        $quantity = $request->integer('quantity');
        $price = (float) $stock->buy_price;

        $itemRequest->update([
            'item_id' => $stock->item_id,
            'request_by' => $request->integer('request_by'),
            'quantity' => $quantity,
            'price' => $price,
            'date_requested' => $request->date('date_requested'),
            'vehicle_id' => $request->integer('vehicle_id'),
            'total' => round($quantity * $price, 2),
        ]);
        $itemRequest->load('item');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::ItemRequestUpdated, "Updated item request for {$itemRequest->item->name}.");

        return redirect()->route('inventory.item-requests.index')->with('success', 'Item request updated.');
    }

    public function destroy(Request $request, ItemRequest $itemRequest, RecordActivity $recordActivity): RedirectResponse
    {
        abort_unless($itemRequest->status === ItemRequestStatus::Pending, 403);

        $itemRequest->load('item');
        $name = $itemRequest->item->name;
        $itemRequest->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::ItemRequestDeleted, "Deleted item request for {$name}.");

        return redirect()->route('inventory.item-requests.index')->with('success', 'Item request deleted.');
    }

    /**
     * @return list<array{id: int, item_name: string, stock_quantity: int, buy_price: string}>
     */
    private function stockOptions(?ItemRequest $current = null): array
    {
        return Stock::query()
            ->with('item')
            ->where(function ($query) use ($current): void {
                $query->where('stock_quantity', '>', 0);

                if ($current instanceof ItemRequest) {
                    $query->orWhere(function ($query) use ($current): void {
                        $query->where('item_id', $current->item_id)
                            ->where('buy_price', $current->price);
                    });
                }
            })
            ->get()
            ->sortBy(fn (Stock $stock): array => [$stock->item->name, $stock->buy_price])
            ->values()
            ->map(fn (Stock $stock): array => [
                'id' => $stock->id,
                'item_name' => $stock->item->name,
                'stock_quantity' => $stock->stock_quantity,
                'buy_price' => $stock->buy_price,
            ])
            ->all();
    }

    private function stockIdFor(ItemRequest $itemRequest): ?int
    {
        $id = Stock::query()
            ->where('item_id', $itemRequest->item_id)
            ->where('buy_price', $itemRequest->price)
            ->value('id');

        return $id === null ? null : (int) $id;
    }

    /**
     * @return list<array{id: int, label: string}>
     */
    private function driverOptions(): array
    {
        return Driver::query()
            ->orderBy('full_name')
            ->get(['id', 'full_name'])
            ->map(fn (Driver $driver): array => [
                'id' => $driver->id,
                'label' => $driver->full_name,
            ])
            ->all();
    }

    /**
     * @return list<array{id: int, label: string}>
     */
    private function vehicleOptions(): array
    {
        return Vehicle::query()
            ->orderBy('plate_no')
            ->get(['id', 'plate_no', 'model'])
            ->map(fn (Vehicle $vehicle): array => [
                'id' => $vehicle->id,
                'label' => "{$vehicle->plate_no} · {$vehicle->model}",
            ])
            ->all();
    }
}
