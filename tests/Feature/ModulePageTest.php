<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders each module page for an authenticated user', function (string $route, string $component) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    ['operations.distribution', 'Operations/Distribution'],
    ['operations.dispatch', 'Operations/Dispatch'],
    ['operations.vehicles.index', 'Operations/Vehicles/Index'],
    ['operations.drivers.index', 'Operations/Drivers/Index'],
    ['finance', 'Finance/Index'],
    ['maintenance.garage.index', 'Maintenance/Garage/Index'],
    ['maintenance.on-road.index', 'Maintenance/OnRoad/Index'],
    ['inventory.items.index', 'Inventory/Items/Index'],
    ['inventory.stocks.index', 'Inventory/Stocks/Index'],
    ['inventory.purchases.index', 'Inventory/Purchases/Index'],
    ['inventory.suppliers.index', 'Inventory/Suppliers/Index'],
    ['inventory.item-requests.index', 'Inventory/ItemRequests/Index'],
    ['inventory.request-approvals', 'Inventory/RequestApprovals/Index'],
    ['hr.employees.index', 'Hr/Employees/Index'],
    ['hr.payroll', 'Hr/Payroll'],
    ['documents.index', 'Documents/Index'],
]);

it('redirects guests from a module page to login', function (string $route) {
    $this->get(route($route))
        ->assertRedirect(route('login'));
})->with([
    'operations.distribution',
    'finance',
    'maintenance.garage.index',
    'inventory.items.index',
    'inventory.stocks.index',
    'hr.employees.index',
]);
