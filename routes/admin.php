<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('categories', CategoryController::class)->except(['show']);
Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])
    ->name('categories.restore')
    ->withTrashed();

Route::resource('brands', BrandController::class)->except(['show']);
Route::post('brands/{brand}/restore', [BrandController::class, 'restore'])
    ->name('brands.restore')
    ->withTrashed();
