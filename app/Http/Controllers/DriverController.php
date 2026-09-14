<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DriverController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $drivers = Driver::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('driver_license_no', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Driver $driver): array => [
                'id' => $driver->id,
                'full_name' => $driver->full_name,
                'driver_license_no' => $driver->driver_license_no,
                'license_type' => $driver->license_type,
                'phone' => $driver->phone,
                'salary' => $driver->salary,
            ]);

        return Inertia::render('Operations/Drivers/Index', [
            'drivers' => $drivers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Operations/Drivers/Create');
    }

    public function store(StoreDriverRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $driver = Driver::query()->create($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::DriverCreated, "Created driver {$driver->full_name}.");

        return redirect()->route('operations.drivers.index')->with('success', 'Driver created.');
    }

    public function edit(Driver $driver): Response
    {
        return Inertia::render('Operations/Drivers/Edit', [
            'driver' => $driver->only([
                'id',
                'full_name',
                'driver_license_no',
                'federal_no',
                'license_type',
                'experience',
                'phone',
                'address',
                'education',
                'salary',
            ]),
        ]);
    }

    public function update(UpdateDriverRequest $request, Driver $driver, RecordActivity $recordActivity): RedirectResponse
    {
        $driver->update($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::DriverUpdated, "Updated driver {$driver->full_name}.");

        return redirect()->route('operations.drivers.index')->with('success', 'Driver updated.');
    }

    public function destroy(Request $request, Driver $driver, RecordActivity $recordActivity): RedirectResponse
    {
        $name = $driver->full_name;
        $driver->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::DriverDeleted, "Deleted driver {$name}.");

        return redirect()->route('operations.drivers.index')->with('success', 'Driver deleted.');
    }
}
