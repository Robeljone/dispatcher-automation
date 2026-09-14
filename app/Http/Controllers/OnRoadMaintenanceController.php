<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreOnRoadMaintenanceRequest;
use App\Http\Requests\UpdateOnRoadMaintenanceRequest;
use App\Models\Driver;
use App\Models\MaintenanceOwn;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OnRoadMaintenanceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $records = MaintenanceOwn::query()
            ->with(['vehicle', 'driver'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('vehicle', function ($query) use ($search): void {
                        $query->where('plate_no', 'like', '%'.$search.'%')
                            ->orWhere('model', 'like', '%'.$search.'%');
                    })->orWhereHas('driver', function ($query) use ($search): void {
                        $query->where('full_name', 'like', '%'.$search.'%');
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (MaintenanceOwn $record): array => [
                'id' => $record->id,
                'plate_no' => $record->vehicle->plate_no,
                'model' => $record->vehicle->model,
                'driver_name' => $record->driver->full_name,
                'work_total' => $record->total,
            ]);

        return Inertia::render('Maintenance/OnRoad/Index', [
            'records' => $records,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Maintenance/OnRoad/Create', [
            'vehicles' => $this->vehicleOptions(),
            'drivers' => $this->driverOptions(),
        ]);
    }

    public function store(StoreOnRoadMaintenanceRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $record = DB::transaction(function () use ($request): MaintenanceOwn {
            $payload = $request->validated();
            $record = MaintenanceOwn::query()->create([
                'vehicle_id' => $payload['vehicle_id'],
                'driver_id' => $payload['driver_id'],
                'total' => $this->lineTotal($payload),
            ]);

            $this->syncLines($record, $payload);

            return $record->load('vehicle');
        });

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::OnRoadMaintenanceCreated, "Created on-road maintenance for {$record->vehicle->plate_no}.");

        return redirect()->route('maintenance.on-road.index')->with('success', 'On-road maintenance created.');
    }

    public function edit(MaintenanceOwn $onRoad): Response
    {
        $onRoad->load(['vehicle', 'driver', 'details']);

        return Inertia::render('Maintenance/OnRoad/Edit', [
            'record' => [
                'id' => $onRoad->id,
                'vehicle_id' => $onRoad->vehicle_id,
                'driver_id' => $onRoad->driver_id,
                'details' => $onRoad->details->map(fn ($detail): array => [
                    'type' => $detail->type,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                ])->all(),
            ],
            'vehicles' => $this->vehicleOptions(),
            'drivers' => $this->driverOptions(),
        ]);
    }

    public function update(UpdateOnRoadMaintenanceRequest $request, MaintenanceOwn $onRoad, RecordActivity $recordActivity): RedirectResponse
    {
        DB::transaction(function () use ($request, $onRoad): void {
            $payload = $request->validated();
            $onRoad->update([
                'vehicle_id' => $payload['vehicle_id'],
                'driver_id' => $payload['driver_id'],
                'total' => $this->lineTotal($payload),
            ]);

            $this->syncLines($onRoad, $payload);
        });

        $onRoad->load('vehicle');

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::OnRoadMaintenanceUpdated, "Updated on-road maintenance for {$onRoad->vehicle->plate_no}.");

        return redirect()->route('maintenance.on-road.index')->with('success', 'On-road maintenance updated.');
    }

    public function destroy(Request $request, MaintenanceOwn $onRoad, RecordActivity $recordActivity): RedirectResponse
    {
        $onRoad->load('vehicle');
        $plate = $onRoad->vehicle->plate_no;
        $onRoad->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::OnRoadMaintenanceDeleted, "Deleted on-road maintenance for {$plate}.");

        return redirect()->route('maintenance.on-road.index')->with('success', 'On-road maintenance deleted.');
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

        return number_format($sum, 2, '.', '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function syncLines(MaintenanceOwn $record, array $payload): void
    {
        $record->details()->delete();

        foreach ($payload['details'] ?? [] as $detail) {
            $price = number_format((float) $detail['price'], 2, '.', '');
            $quantity = (int) ($detail['quantity'] ?? 1);
            $record->details()->create([
                'parent_id' => (string) $record->id,
                'type' => $detail['type'],
                'quantity' => $quantity,
                'price' => $price,
                'total' => number_format($this->lineAmount($detail), 2, '.', ''),
            ]);
        }
    }
}
