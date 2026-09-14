<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Enums\ItemStatus;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Supplier $supplier): array => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'status' => $supplier->status->value,
                'status_label' => $supplier->status->label(),
            ]);

        return Inertia::render('Inventory/Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Inventory/Suppliers/Create', [
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(StoreSupplierRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $supplier = Supplier::query()->create($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::SupplierCreated, "Created supplier {$supplier->name}.");

        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier created.');
    }

    public function edit(Supplier $supplier): Response
    {
        return Inertia::render('Inventory/Suppliers/Edit', [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'address' => $supplier->address,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'status' => $supplier->status->value,
            ],
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier, RecordActivity $recordActivity): RedirectResponse
    {
        $supplier->update($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::SupplierUpdated, "Updated supplier {$supplier->name}.");

        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Request $request, Supplier $supplier, RecordActivity $recordActivity): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::SupplierDeleted, "Deleted supplier {$name}.");

        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier deleted.');
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
