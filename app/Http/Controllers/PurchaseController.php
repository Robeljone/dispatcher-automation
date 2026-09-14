<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Enums\PurchaseStatus;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $purchases = Purchase::query()
            ->with(['item', 'supplier'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('item', function ($query) use ($search): void {
                        $query->where('name', 'like', '%'.$search.'%');
                    })->orWhereHas('supplier', function ($query) use ($search): void {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Purchase $purchase): array => [
                'id' => $purchase->id,
                'item_name' => $purchase->item->name,
                'supplier_name' => $purchase->supplier->name,
                'quantity' => $purchase->quantity,
                'price' => $purchase->price,
                'status' => $purchase->status->value,
                'status_label' => $purchase->status->label(),
                'has_attachment' => $purchase->attachment !== null,
            ]);

        return Inertia::render('Inventory/Purchases/Index', [
            'purchases' => $purchases,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Inventory/Purchases/Create', [
            'items' => $this->itemOptions(),
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function store(StorePurchaseRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $attachment = $this->storeAttachment($request->file('attachment'));

        $purchase = Purchase::query()->create([
            'item_id' => $request->integer('item_id'),
            'quantity' => $request->integer('quantity'),
            'supplier_id' => $request->integer('supplier_id'),
            'price' => $request->string('price')->toString(),
            'attachment' => $attachment,
            'status' => PurchaseStatus::Pending,
            'approved' => false,
        ]);
        $purchase->load('item');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::PurchaseCreated, "Created purchase for {$purchase->item->name}.");

        return redirect()->route('inventory.purchases.index')->with('success', 'Purchase created.');
    }

    public function edit(Purchase $purchase): Response
    {
        $purchase->load(['item', 'supplier']);

        return Inertia::render('Inventory/Purchases/Edit', [
            'purchase' => [
                'id' => $purchase->id,
                'item_id' => $purchase->item_id,
                'quantity' => $purchase->quantity,
                'supplier_id' => $purchase->supplier_id,
                'price' => $purchase->price,
                'has_attachment' => $purchase->attachment !== null,
                'can_edit' => $purchase->status === PurchaseStatus::Pending,
            ],
            'items' => $this->itemOptions(),
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase, RecordActivity $recordActivity): RedirectResponse
    {
        $payload = [
            'item_id' => $request->integer('item_id'),
            'quantity' => $request->integer('quantity'),
            'supplier_id' => $request->integer('supplier_id'),
            'price' => $request->string('price')->toString(),
        ];

        $uploaded = $request->file('attachment');
        if ($uploaded instanceof UploadedFile) {
            if ($purchase->attachment !== null) {
                Storage::disk('local')->delete($purchase->attachment);
            }
            $payload['attachment'] = $this->storeAttachment($uploaded);
        }

        $purchase->update($payload);
        $purchase->load('item');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::PurchaseUpdated, "Updated purchase for {$purchase->item->name}.");

        return redirect()->route('inventory.purchases.index')->with('success', 'Purchase updated.');
    }

    public function destroy(Request $request, Purchase $purchase, RecordActivity $recordActivity): RedirectResponse
    {
        abort_unless($purchase->status === PurchaseStatus::Pending, 403);

        $purchase->load('item');
        $name = $purchase->item->name;

        if ($purchase->attachment !== null) {
            Storage::disk('local')->delete($purchase->attachment);
        }

        $purchase->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::PurchaseDeleted, "Deleted purchase for {$name}.");

        return redirect()->route('inventory.purchases.index')->with('success', 'Purchase deleted.');
    }

    public function download(Purchase $purchase): StreamedResponse
    {
        abort_unless($purchase->attachment !== null && Storage::disk('local')->exists($purchase->attachment), 404);

        return Storage::disk('local')->download($purchase->attachment);
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

    /**
     * @return list<array{id: int, name: string}>
     */
    private function supplierOptions(): array
    {
        return Supplier::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Supplier $supplier): array => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ])
            ->all();
    }

    private function storeAttachment(?UploadedFile $file): ?string
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        return $file->store('purchase-attachments', 'local');
    }
}
