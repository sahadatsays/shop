<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('products', ProductController::class);
Route::post('products/{product}/restore', [ProductController::class, 'restore'])
    ->name('products.restore')
    ->withTrashed();

Route::resource('categories', CategoryController::class);
Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])
    ->name('categories.restore')
    ->withTrashed();

Route::resource('brands', BrandController::class);
Route::post('brands/{brand}/restore', [BrandController::class, 'restore'])
    ->name('brands.restore')
    ->withTrashed();
