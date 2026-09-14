<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AppearanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentExplorerController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\DocumentFolderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GarageMaintenanceController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\OnRoadMaintenanceController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RequestApprovalController;
use App\Http\Controllers\SidebarPreferenceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::redirect('operations', '/operations/distribution');
    Route::get('operations/distribution', [OperationController::class, 'distribution'])->name('operations.distribution');
    Route::get('operations/dispatch', [OperationController::class, 'dispatch'])->name('operations.dispatch');
    Route::resource('operations/vehicles', VehicleController::class)
        ->except(['show'])
        ->names('operations.vehicles');
    Route::resource('operations/drivers', DriverController::class)
        ->except(['show'])
        ->names('operations.drivers');
    Route::get('finance', FinanceController::class)->name('finance');
    Route::redirect('maintenance', '/maintenance/garage');
    Route::resource('maintenance/garage', GarageMaintenanceController::class)
        ->except(['show'])
        ->parameters(['garage' => 'garage'])
        ->names('maintenance.garage');
    Route::resource('maintenance/on-road', OnRoadMaintenanceController::class)
        ->except(['show'])
        ->parameters(['on-road' => 'onRoad'])
        ->names('maintenance.on-road');
    Route::redirect('inventory', '/inventory/items');
    Route::redirect('inventory/store', '/inventory/stocks');
    Route::resource('inventory/items', ItemController::class)
        ->except(['show'])
        ->names('inventory.items');
    Route::resource('inventory/stocks', StockController::class)
        ->except(['show'])
        ->names('inventory.stocks');
    Route::resource('inventory/purchases', PurchaseController::class)
        ->except(['show'])
        ->names('inventory.purchases');
    Route::get('inventory/purchases/{purchase}/download', [PurchaseController::class, 'download'])
        ->name('inventory.purchases.download');
    Route::resource('inventory/suppliers', SupplierController::class)
        ->except(['show'])
        ->names('inventory.suppliers');
    Route::resource('inventory/item-requests', ItemRequestController::class)
        ->except(['show'])
        ->names('inventory.item-requests');
    Route::get('inventory/request-approvals', [RequestApprovalController::class, 'index'])->name('inventory.request-approvals');
    Route::patch('inventory/request-approvals/purchases/{purchase}', [RequestApprovalController::class, 'updatePurchase'])
        ->name('inventory.request-approvals.purchases.update');
    Route::patch('inventory/request-approvals/item-requests/{item_request}', [RequestApprovalController::class, 'updateItemRequest'])
        ->name('inventory.request-approvals.item-requests.update');
    Route::redirect('hr', '/hr/employees');
    Route::resource('hr/employees', EmployeeController::class)
        ->except(['show'])
        ->names('hr.employees');
    Route::get('hr/payroll', [HrController::class, 'payroll'])->name('hr.payroll');
    Route::get('documents', DocumentExplorerController::class)->name('documents.index');
    Route::post('documents/folders', [DocumentFolderController::class, 'store'])->name('documents.folders.store');
    Route::patch('documents/folders/{documentFolder}', [DocumentFolderController::class, 'update'])->name('documents.folders.update');
    Route::delete('documents/folders/{documentFolder}', [DocumentFolderController::class, 'destroy'])->name('documents.folders.destroy');
    Route::post('documents/files', [DocumentFileController::class, 'store'])->name('documents.files.store');
    Route::get('documents/files/{documentFile}/download', [DocumentFileController::class, 'download'])->name('documents.files.download');
    Route::delete('documents/files/{documentFile}', [DocumentFileController::class, 'destroy'])->name('documents.files.destroy');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('appearance', [AppearanceController::class, 'update'])->name('appearance.update');
    Route::patch('sidebar', [SidebarPreferenceController::class, 'update'])->name('sidebar.update');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');
    });
});
