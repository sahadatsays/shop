<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('products', ProductController::class);
Route::post('products/{product}/restore', [ProductController::class, 'restore'])
    ->name('products.restore')
    ->withTrashed();

Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
Route::get('inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
Route::get('inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
Route::post('inventory/{product}/adjust', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjust.store');

Route::resource('categories', CategoryController::class);
Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])
    ->name('categories.restore')
    ->withTrashed();

Route::resource('brands', BrandController::class);
Route::post('brands/{brand}/restore', [BrandController::class, 'restore'])
    ->name('brands.restore')
    ->withTrashed();

Route::resource('customers', CustomerController::class);
Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])
    ->name('customers.restore')
    ->withTrashed();
Route::post('customers/{customer}/notes', [CustomerController::class, 'storeNote'])
    ->name('customers.notes.store');
