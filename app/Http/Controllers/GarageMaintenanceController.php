<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreGarageMaintenanceRequest;
use App\Http\Requests\UpdateGarageMaintenanceRequest;
use App\Models\MaintenanceGarage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GarageMaintenanceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $records = MaintenanceGarage::query()
            ->with(['vehicle'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('vehicle', function ($query) use ($search): void {
                    $query->where('plate_no', 'like', '%'.$search.'%')
                        ->orWhere('model', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (MaintenanceGarage $record): array => [
                'id' => $record->id,
                'plate_no' => $record->vehicle->plate_no,
                'model' => $record->vehicle->model,
                'prev_main_date' => $record->prev_main_date?->toDateString(),
                'prev_main_km' => $record->prev_main_km,
                'current_km' => $record->current_km,
                'work_total' => $record->total,
            ]);

        return Inertia::render('Maintenance/Garage/Index', [
            'records' => $records,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Maintenance/Garage/Create', [
            'vehicles' => $this->vehicleOptions(),
        ]);
    }

    public function store(StoreGarageMaintenanceRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $record = DB::transaction(function () use ($request): MaintenanceGarage {
            $payload = $request->validated();
            $record = MaintenanceGarage::query()->create([
                'vehicle_id' => $payload['vehicle_id'],
                'prev_main_date' => $payload['prev_main_date'] ?? null,
                'prev_main_km' => $payload['prev_main_km'] ?? null,
                'current_km' => $payload['current_km'],
                'inspection_detail' => $payload['inspection_detail'] ?? null,
                'total' => $this->lineTotal($payload),
            ]);

            $this->syncLines($record, $payload);

            return $record->load('vehicle');
        });

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::GarageMaintenanceCreated, "Created garage maintenance for {$record->vehicle->plate_no}.");

        return redirect()->route('maintenance.garage.index')->with('success', 'Garage maintenance created.');
    }

    public function edit(MaintenanceGarage $garage): Response
    {
        $garage->load(['vehicle', 'details', 'spares']);

        return Inertia::render('Maintenance/Garage/Edit', [
            'record' => [
                'id' => $garage->id,
                'vehicle_id' => $garage->vehicle_id,
                'prev_main_date' => $garage->prev_main_date?->toDateString(),
                'prev_main_km' => $garage->prev_main_km,
                'current_km' => $garage->current_km,
                'inspection_detail' => $garage->inspection_detail,
                'details' => $garage->details->map(fn ($detail): array => [
                    'type' => $detail->type,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                ])->all(),
                'spares' => $garage->spares->map(fn ($spare): array => [
                    'name' => $spare->name,
                    'quantity' => $spare->quantity,
                    'price' => $spare->price,
                ])->all(),
            ],
            'vehicles' => $this->vehicleOptions(),
        ]);
    }

    public function update(UpdateGarageMaintenanceRequest $request, MaintenanceGarage $garage, RecordActivity $recordActivity): RedirectResponse
    {
        DB::transaction(function () use ($request, $garage): void {
            $payload = $request->validated();
            $garage->update([
                'vehicle_id' => $payload['vehicle_id'],
                'prev_main_date' => $payload['prev_main_date'] ?? null,
                'prev_main_km' => $payload['prev_main_km'] ?? null,
                'current_km' => $payload['current_km'],
                'inspection_detail' => $payload['inspection_detail'] ?? null,
                'total' => $this->lineTotal($payload),
            ]);

            $this->syncLines($garage, $payload);
        });

        $garage->load('vehicle');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::GarageMaintenanceUpdated, "Updated garage maintenance for {$garage->vehicle->plate_no}.");

        return redirect()->route('maintenance.garage.index')->with('success', 'Garage maintenance updated.');
    }

    public function destroy(Request $request, MaintenanceGarage $garage, RecordActivity $recordActivity): RedirectResponse
    {
        $garage->load('vehicle');
        $plate = $garage->vehicle->plate_no;
        $garage->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::GarageMaintenanceDeleted, "Deleted garage maintenance for {$plate}.");

        return redirect()->route('maintenance.garage.index')->with('success', 'Garage maintenance deleted.');
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

    /**
     * @param  array<string, mixed>  $line
     */
    private function lineAmount(array $line): float
    {
        return (int) ($line['quantity'] ?? 1) * (float) $line['price'];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function lineTotal(array $payload): string
    {
        $sum = 0.0;

        foreach ($payload['details'] ?? [] as $detail) {
            $sum += $this->lineAmount($detail);
        }

        foreach ($payload['spares'] ?? [] as $spare) {
            $sum += $this->lineAmount($spare);
        }

        return number_format($sum, 2, '.', '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function syncLines(MaintenanceGarage $record, array $payload): void
    {
        $record->details()->delete();
        $record->spares()->delete();

        foreach ($payload['details'] ?? [] as $detail) {
            $price = number_format((float) $detail['price'], 2, '.', '');
            $quantity = (int) ($detail['quantity'] ?? 1);
            $record->details()->create([
                'type' => $detail['type'],
                'quantity' => $quantity,
                'price' => $price,
                'total' => number_format($this->lineAmount($detail), 2, '.', ''),
            ]);
        }

        foreach ($payload['spares'] ?? [] as $spare) {
            $price = number_format((float) $spare['price'], 2, '.', '');
            $quantity = (int) ($spare['quantity'] ?? 1);
            $record->spares()->create([
                'name' => $spare['name'],
                'quantity' => $quantity,
                'price' => $price,
                'total' => number_format($this->lineAmount($spare), 2, '.', ''),
            ]);
        }
    }
}
