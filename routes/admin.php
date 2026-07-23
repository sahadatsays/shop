<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerPromotionController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CountdownPromotionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionMatrixController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleProductController;
use App\Http\Controllers\Admin\StoreSettingsController;
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

Route::middleware('admin.permission:discounts.view')->group(function (): void {
    Route::resource('discounts', DiscountController::class)->except(['show']);
});

Route::middleware('admin.permission:offers.view')->group(function (): void {
    Route::resource('offers', OfferController::class);
});

Route::middleware('admin.permission:sale-products.view')->group(function (): void {
    Route::get('sale-products', [SaleProductController::class, 'index'])->name('sale-products.index');
    Route::patch('sale-products/{product}', [SaleProductController::class, 'update'])
        ->middleware('admin.permission:sale-products.manage')
        ->name('sale-products.update');
});

Route::middleware('admin.permission:collections.view')->group(function (): void {
    Route::resource('collections', CollectionController::class);
});

Route::middleware('admin.permission:promotions.view')->group(function (): void {
    Route::resource('banner-promotions', BannerPromotionController::class)
        ->parameters(['banner-promotions' => 'promotion'])
        ->except(['show']);
    Route::resource('countdown-promotions', CountdownPromotionController::class)
        ->parameters(['countdown-promotions' => 'promotion'])
        ->except(['show']);
});

Route::middleware('admin.permission:settings.view')->group(function (): void {
    Route::get('settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [StoreSettingsController::class, 'update'])
        ->middleware('admin.permission:settings.manage')
        ->name('settings.update');
});

Route::middleware('admin.permission:notifications.view')->group(function (): void {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

Route::middleware('admin.permission:media.view')->group(function (): void {
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::get('media/picker', [MediaController::class, 'picker'])->name('media.picker');
    Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');
});

Route::middleware('admin.permission:media.manage')->group(function (): void {
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::patch('media/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::post('media/{media}/crop', [MediaController::class, 'crop'])->name('media.crop');
    Route::post('media/{media}/optimize', [MediaController::class, 'optimize'])->name('media.optimize');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::middleware('admin.permission:media.folders.manage')->group(function (): void {
    Route::post('media/folders', [MediaFolderController::class, 'store'])->name('media.folders.store');
    Route::patch('media/folders/{folder}', [MediaFolderController::class, 'update'])->name('media.folders.update');
    Route::delete('media/folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media.folders.destroy');
});
