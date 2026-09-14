<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $stocks = Stock::query()
            ->with('item')
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('item', function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('brand', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Stock $stock): array => [
                'id' => $stock->id,
                'item_name' => $stock->item->name,
                'item_brand' => $stock->item->brand,
                'stock_quantity' => $stock->stock_quantity,
                'min_quantity' => $stock->min_quantity,
                'buy_price' => $stock->buy_price,
                'is_low' => $stock->stock_quantity <= $stock->min_quantity,
            ]);

        return Inertia::render('Inventory/Stocks/Index', [
            'stocks' => $stocks,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Inventory/Stocks/Create', [
            'items' => $this->itemOptions(),
        ]);
    }

    public function store(StoreStockRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $stock = Stock::query()->create($request->validated());
        $stock->load('item');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::StockCreated, "Created stock for {$stock->item->name}.");

        return redirect()->route('inventory.stocks.index')->with('success', 'Stock created.');
    }

    public function edit(Stock $stock): Response
    {
        $stock->load('item');

        return Inertia::render('Inventory/Stocks/Edit', [
            'stock' => [
                'id' => $stock->id,
                'item_id' => $stock->item_id,
                'item_name' => $stock->item->name,
                'stock_quantity' => $stock->stock_quantity,
                'min_quantity' => $stock->min_quantity,
                'buy_price' => $stock->buy_price,
            ],
        ]);
    }

    public function update(UpdateStockRequest $request, Stock $stock, RecordActivity $recordActivity): RedirectResponse
    {
        $stock->update($request->validated());
        $stock->load('item');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::StockUpdated, "Updated stock for {$stock->item->name}.");

        return redirect()->route('inventory.stocks.index')->with('success', 'Stock updated.');
    }

    public function destroy(Request $request, Stock $stock, RecordActivity $recordActivity): RedirectResponse
    {
        $stock->load('item');
        $name = $stock->item->name;
        $stock->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::StockDeleted, "Deleted stock for {$name}.");

        return redirect()->route('inventory.stocks.index')->with('success', 'Stock deleted.');
    }

    /**
     * @return list<array{id: int, name: string, brand: string}>
     */
    private function itemOptions(): array
    {
        return Item::query()
            ->orderBy('name')
            ->get(['id', 'name', 'brand'])
            ->map(fn (Item $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'brand' => $item->brand,
            ])
            ->all();
    }
}
