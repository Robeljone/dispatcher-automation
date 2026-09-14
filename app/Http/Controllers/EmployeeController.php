<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $employees = Employee::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('position', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Employee $employee): array => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'phone' => $employee->phone,
                'position' => $employee->position,
                'salary' => $employee->salary,
            ]);

        return Inertia::render('Hr/Employees/Index', [
            'employees' => $employees,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Hr/Employees/Create');
    }

    public function store(StoreEmployeeRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $employee = Employee::query()->create($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::EmployeeCreated, "Created employee {$employee->full_name}.");

        return redirect()->route('hr.employees.index')->with('success', 'Employee created.');
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Hr/Employees/Edit', [
            'employee' => $employee->only([
                'id',
                'full_name',
                'phone',
                'address',
                'education',
                'position',
                'salary',
            ]),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee, RecordActivity $recordActivity): RedirectResponse
    {
        $employee->update($request->validated());

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::EmployeeUpdated, "Updated employee {$employee->full_name}.");

        return redirect()->route('hr.employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Request $request, Employee $employee, RecordActivity $recordActivity): RedirectResponse
    {
        $name = $employee->full_name;
        $employee->delete();

        $actor = $request->user();
        assert($actor instanceof User);

        $recordActivity->handle($actor, ActivityAction::EmployeeDeleted, "Deleted employee {$name}.");

        return redirect()->route('hr.employees.index')->with('success', 'Employee deleted.');
    }
}
