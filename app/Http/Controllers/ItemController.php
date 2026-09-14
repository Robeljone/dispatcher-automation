<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Enums\ItemStatus;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $items = Item::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('brand', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Item $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'brand' => $item->brand,
                'status' => $item->status->value,
                'status_label' => $item->status->label(),
            ]);

        return Inertia::render('Inventory/Items/Index', [
            'items' => $items,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Inventory/Items/Create', [
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(StoreItemRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $item = Item::query()->create($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::ItemCreated, "Created item {$item->name}.");

        return redirect()->route('inventory.items.index')->with('success', 'Item created.');
    }

    public function edit(Item $item): Response
    {
        return Inertia::render('Inventory/Items/Edit', [
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'brand' => $item->brand,
                'status' => $item->status->value,
            ],
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item, RecordActivity $recordActivity): RedirectResponse
    {
        $item->update($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::ItemUpdated, "Updated item {$item->name}.");

        return redirect()->route('inventory.items.index')->with('success', 'Item updated.');
    }

    public function destroy(Request $request, Item $item, RecordActivity $recordActivity): RedirectResponse
    {
        $name = $item->name;
        $item->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::ItemDeleted, "Deleted item {$name}.");

        return redirect()->route('inventory.items.index')->with('success', 'Item deleted.');
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function statuses(): array
    {
        return array_map(
            fn (ItemStatus $status): array => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            ItemStatus::cases(),
        );
    }
}
