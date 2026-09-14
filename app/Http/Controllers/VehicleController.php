<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $vehicles = Vehicle::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('plate_no', 'like', '%'.$search.'%')
                        ->orWhere('model', 'like', '%'.$search.'%')
                        ->orWhere('chassis_no', 'like', '%'.$search.'%')
                        ->orWhere('vehicle_type', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Vehicle $vehicle): array => [
                'id' => $vehicle->id,
                'vehicle_type' => $vehicle->vehicle_type,
                'model' => $vehicle->model,
                'made_year' => $vehicle->made_year,
                'plate_no' => $vehicle->plate_no,
                'color' => $vehicle->color,
                'fuel_type' => $vehicle->fuel_type,
            ]);

        return Inertia::render('Operations/Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Operations/Vehicles/Create');
    }

    public function store(StoreVehicleRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $vehicle = Vehicle::query()->create($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::VehicleCreated, "Created vehicle {$vehicle->plate_no}.");

        return redirect()->route('operations.vehicles.index')->with('success', 'Vehicle created.');
    }

    public function edit(Vehicle $vehicle): Response
    {
        return Inertia::render('Operations/Vehicles/Edit', [
            'vehicle' => $vehicle->only([
                'id',
                'vehicle_type',
                'made_in',
                'model',
                'made_year',
                'chassis_no',
                'motor_no',
                'body_type',
                'color',
                'fuel_type',
                'motor_cc',
                'weight_total',
                'weight_single',
                'load_amount',
                'motor_capacity',
                'total_cylinder',
                'plate_no',
                'pre_plate_no',
            ]),
        ]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle, RecordActivity $recordActivity): RedirectResponse
    {
        $vehicle->update($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::VehicleUpdated, "Updated vehicle {$vehicle->plate_no}.");

        return redirect()->route('operations.vehicles.index')->with('success', 'Vehicle updated.');
    }

    public function destroy(Request $request, Vehicle $vehicle, RecordActivity $recordActivity): RedirectResponse
    {
        $plateNo = $vehicle->plate_no;
        $vehicle->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::VehicleDeleted, "Deleted vehicle {$plateNo}.");

        return redirect()->route('operations.vehicles.index')->with('success', 'Vehicle deleted.');
    }
}
