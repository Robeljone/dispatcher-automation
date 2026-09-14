<?php

namespace App\Http\Controllers;

use App\Actions\AdjustStock;
use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Enums\ItemRequestStatus;
use App\Enums\PurchaseStatus;
use App\Models\ItemRequest;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RequestApprovalController extends Controller
{
    public function index(Request $request): Response
    {
        $purchases = Purchase::query()
            ->with(['item', 'supplier'])
            ->where('status', PurchaseStatus::Pending)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Purchase $purchase): array => [
                'id' => $purchase->id,
                'item_name' => $purchase->item->name,
                'supplier_name' => $purchase->supplier->name,
                'quantity' => $purchase->quantity,
                'price' => $purchase->price,
            ]);

        $itemRequests = ItemRequest::query()
            ->with(['item', 'vehicle', 'requester'])
            ->where('status', ItemRequestStatus::Pending)
            ->orderByDesc('id')
            ->get()
            ->map(fn (ItemRequest $itemRequest): array => [
                'id' => $itemRequest->id,
                'item_name' => $itemRequest->item->name,
                'quantity' => $itemRequest->quantity,
                'vehicle' => $itemRequest->vehicle->plate_no,
                'driver' => $itemRequest->requester->full_name,
                'total' => $itemRequest->total,
            ]);

        return Inertia::render('Inventory/RequestApprovals/Index', [
            'purchases' => $purchases,
            'itemRequests' => $itemRequests,
            'tab' => $this->tab($request, 'purchases'),
        ]);
    }

    public function updatePurchase(Request $request, Purchase $purchase, AdjustStock $adjustStock, RecordActivity $recordActivity): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::enum(PurchaseStatus::class)],
        ]);

        abort_unless($purchase->status === PurchaseStatus::Pending, 403);

        $decision = PurchaseStatus::from($validated['decision']);
        abort_if($decision === PurchaseStatus::Pending, 422);

        $actor = $request->user();
        assert($actor instanceof User);

        DB::transaction(function () use ($purchase, $decision, $adjustStock): void {
            $locked = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);
            $locked->load('item');
            $locked->status = $decision;
            $locked->approved = $decision === PurchaseStatus::Approved;
            $locked->save();

            if ($decision === PurchaseStatus::Approved) {
                $adjustStock->handle($locked->item, $locked->quantity, (string) $locked->price);
            }
        });

        $purchase->load('item');
        $action = $decision === PurchaseStatus::Approved
            ? ActivityAction::PurchaseApproved
            : ActivityAction::PurchaseRejected;
        $recordActivity->handle($actor, $action, "{$decision->label()} purchase for {$purchase->item->name}.");

        return $this->redirectToTab($request, 'purchases')->with('success', "Purchase {$decision->value}.");
    }

    public function updateItemRequest(Request $request, ItemRequest $itemRequest, AdjustStock $adjustStock, RecordActivity $recordActivity): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::enum(ItemRequestStatus::class)],
        ]);

        abort_unless($itemRequest->status === ItemRequestStatus::Pending, 403);

        $decision = ItemRequestStatus::from($validated['decision']);
        abort_if($decision === ItemRequestStatus::Pending, 422);

        $actor = $request->user();
        assert($actor instanceof User);

        DB::transaction(function () use ($itemRequest, $decision, $adjustStock): void {
            $locked = ItemRequest::query()->lockForUpdate()->findOrFail($itemRequest->id);
            $locked->load('item');

            if ($decision === ItemRequestStatus::Approved) {
                $adjustStock->handle($locked->item, -$locked->quantity, (string) $locked->price);
            }

            $locked->status = $decision;
            $locked->save();
        });

        $itemRequest->load('item');
        $action = $decision === ItemRequestStatus::Approved
            ? ActivityAction::ItemRequestApproved
            : ActivityAction::ItemRequestRejected;
        $recordActivity->handle($actor, $action, "{$decision->label()} item request for {$itemRequest->item->name}.");

        return $this->redirectToTab($request, 'item-requests')->with('success', "Item request {$decision->value}.");
    }

    private function redirectToTab(Request $request, string $default): RedirectResponse
    {
        return redirect()->route('inventory.request-approvals', [
            'tab' => $this->tab($request, $default),
        ]);
    }

    private function tab(Request $request, string $default): string
    {
        $tab = $request->string('tab')->toString();

        return in_array($tab, ['purchases', 'item-requests'], true) ? $tab : $default;
    }
}
