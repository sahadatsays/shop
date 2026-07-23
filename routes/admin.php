<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionMatrixController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

Route::middleware('admin.permission:dashboard.view')->get('/', DashboardController::class)->name('dashboard');

Route::middleware('admin.permission:products.view')->group(function (): void {
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/restore', [ProductController::class, 'restore'])
        ->name('products.restore')
        ->withTrashed();
});

Route::middleware('admin.permission:inventory.view')->group(function (): void {
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::get('inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('inventory/{product}/adjust', [InventoryController::class, 'storeAdjustment'])
        ->middleware('admin.permission:inventory.manage')
        ->name('inventory.adjust.store');
});

Route::middleware('admin.permission:categories.view')->group(function (): void {
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->name('categories.restore')
        ->withTrashed();
});

Route::middleware('admin.permission:brands.view')->group(function (): void {
    Route::resource('brands', BrandController::class);
    Route::post('brands/{brand}/restore', [BrandController::class, 'restore'])
        ->name('brands.restore')
        ->withTrashed();
});

Route::middleware('admin.permission:customers.view')->group(function (): void {
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])
        ->name('customers.restore')
        ->withTrashed();
    Route::post('customers/{customer}/notes', [CustomerController::class, 'storeNote'])
        ->middleware('admin.permission:customers.manage')
        ->name('customers.notes.store');
});

Route::middleware('admin.permission:orders.view')->group(function (): void {
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.status.update');
    Route::post('orders/{order}/notes', [OrderController::class, 'storeNote'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.notes.store');
});

Route::middleware('admin.permission:roles.view')->group(function (): void {
    Route::resource('roles', RoleController::class)->except(['destroy']);
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('admin.permission:roles.manage')
        ->name('roles.destroy');
    Route::get('roles-matrix', [PermissionMatrixController::class, 'edit'])
        ->middleware('admin.permission:access-matrix.manage')
        ->name('roles.matrix');
    Route::put('roles-matrix', [PermissionMatrixController::class, 'update'])
        ->middleware('admin.permission:access-matrix.manage')
        ->name('roles.matrix.update');
});

Route::middleware('admin.permission:permissions.view')->group(function (): void {
    Route::resource('permissions', PermissionController::class)->except(['destroy']);
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('admin.permission:permissions.manage')
        ->name('permissions.destroy');
});
