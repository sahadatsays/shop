<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerPromotionController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CountdownPromotionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DashboardWidgetController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\HeroBannerController;
use App\Http\Controllers\Admin\HomepageFeatureController;
use App\Http\Controllers\Admin\HomepageReviewController;
use App\Http\Controllers\Admin\HomepageSettingController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionMatrixController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoBannerController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleProductController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

Route::middleware('admin.permission:dashboard.view')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/widgets/{key}', [DashboardController::class, 'widget'])->name('dashboard.widget');
    Route::post('dashboard/preferences', [DashboardController::class, 'savePreferences'])->name('dashboard.preferences.save');
    Route::post('dashboard/preferences/reset', [DashboardController::class, 'resetPreferences'])->name('dashboard.preferences.reset');
});

Route::middleware('admin.permission:dashboard.widgets.view')->group(function (): void {
    Route::get('dashboard-widgets', [DashboardWidgetController::class, 'index'])->name('dashboard-widgets.index');

    Route::middleware('admin.permission:dashboard.widgets.manage')->group(function (): void {
        Route::get('dashboard-widgets/create', [DashboardWidgetController::class, 'create'])->name('dashboard-widgets.create');
        Route::post('dashboard-widgets', [DashboardWidgetController::class, 'store'])->name('dashboard-widgets.store');
        Route::get('dashboard-widgets/{dashboardWidget}/edit', [DashboardWidgetController::class, 'edit'])->name('dashboard-widgets.edit');
        Route::put('dashboard-widgets/{dashboardWidget}', [DashboardWidgetController::class, 'update'])->name('dashboard-widgets.update');
        Route::patch('dashboard-widgets/{dashboardWidget}/toggle', [DashboardWidgetController::class, 'toggle'])->name('dashboard-widgets.toggle');
        Route::delete('dashboard-widgets/{dashboardWidget}', [DashboardWidgetController::class, 'destroy'])->name('dashboard-widgets.destroy');
    });
});

Route::middleware('admin.permission:products.view')->group(function (): void {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::middleware('admin.permission:products.manage')->group(function (): void {
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::patch('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
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

Route::middleware('admin.permission:warehouses.view')->group(function (): void {
    Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
});

Route::middleware('admin.permission:warehouses.manage')->group(function (): void {
    Route::get('warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::put('warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::patch('warehouses/{warehouse}', [WarehouseController::class, 'update']);
    Route::delete('warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
});

Route::middleware('admin.permission:suppliers.view')->group(function (): void {
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
});

Route::middleware('admin.permission:suppliers.create')->group(function (): void {
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
});

Route::middleware('admin.permission:suppliers.view')->group(function (): void {
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
});

Route::middleware('admin.permission:suppliers.edit')->group(function (): void {
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::patch('suppliers/{supplier}', [SupplierController::class, 'update']);
});

Route::middleware('admin.permission:suppliers.delete')->group(function (): void {
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});

Route::middleware('admin.permission:purchases.view')->group(function (): void {
    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
});

Route::middleware('admin.permission:purchases.create')->group(function (): void {
    Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('purchases-products/search', [PurchaseController::class, 'searchProducts'])->name('purchases.products.search');
});

Route::middleware('admin.permission:purchases.view')->group(function (): void {
    Route::get('purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
});

Route::middleware('admin.permission:purchases.edit')->group(function (): void {
    Route::get('purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::patch('purchases/{purchase}', [PurchaseController::class, 'update']);
});

Route::middleware('admin.permission:purchases.create')->group(function (): void {
    Route::post('purchases/{purchase}/submit', [PurchaseController::class, 'submit'])->name('purchases.submit');
});

Route::middleware('admin.permission:purchases.approve')->group(function (): void {
    Route::post('purchases/{purchase}/approve', [PurchaseController::class, 'approve'])->name('purchases.approve');
});

Route::middleware('admin.permission:purchases.receive')->group(function (): void {
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
});

Route::middleware('admin.permission:purchases.cancel')->group(function (): void {
    Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
});

Route::middleware('admin.permission:categories.view')->group(function (): void {
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
});

Route::middleware('admin.permission:categories.manage')->group(function (): void {
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->name('categories.restore')
        ->withTrashed();
});

Route::middleware('admin.permission:brands.view')->group(function (): void {
    Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
});

Route::middleware('admin.permission:brands.manage')->group(function (): void {
    Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::patch('brands/{brand}', [BrandController::class, 'update']);
    Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('brands/{brand}/restore', [BrandController::class, 'restore'])
        ->name('brands.restore')
        ->withTrashed();
});

Route::middleware('admin.permission:customers.view')->group(function (): void {
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
});

Route::middleware('admin.permission:customers.manage')->group(function (): void {
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::patch('customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])
        ->name('customers.restore')
        ->withTrashed();
    Route::post('customers/{customer}/notes', [CustomerController::class, 'storeNote'])->name('customers.notes.store');
});

Route::middleware('admin.permission:orders.view')->group(function (): void {
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.store');
    Route::get('orders/search/customers', [OrderController::class, 'searchCustomers'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.search.customers');
    Route::get('orders/search/products', [OrderController::class, 'searchProducts'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.search.products');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])
        ->name('orders.invoice');
    Route::post('orders/{order}/payments', [OrderController::class, 'storePayment'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.payments.store');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.status.update');
    Route::patch('orders/{order}/status/next', [OrderController::class, 'advanceStatus'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.status.advance');
    Route::post('orders/{order}/notes', [OrderController::class, 'storeNote'])
        ->middleware('admin.permission:orders.manage')
        ->name('orders.notes.store');
});

Route::middleware('admin.permission:refunds.view')->group(function (): void {
    Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/{refund}', [RefundController::class, 'show'])->name('refunds.show');
    Route::post('orders/{order}/refunds', [RefundController::class, 'store'])
        ->middleware('admin.permission:refunds.manage')
        ->name('orders.refunds.store');
});

Route::middleware('admin.permission:users.view')->group(function (): void {
    Route::resource('users', UserController::class);
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

Route::middleware('admin.permission:homepage.view')->prefix('homepage')->name('homepage.')->group(function (): void {
    Route::get('settings', [HomepageSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [HomepageSettingController::class, 'update'])
        ->middleware('admin.permission:homepage.manage')
        ->name('settings.update');

    Route::resource('hero-banners', HeroBannerController::class)->except(['show']);
    Route::resource('promo-banners', PromoBannerController::class)->except(['show']);
    Route::resource('features', HomepageFeatureController::class)
        ->parameters(['features' => 'feature'])
        ->except(['show']);
    Route::resource('reviews', HomepageReviewController::class)->except(['show']);

    Route::resource('menus', MenuController::class)->only(['index', 'edit']);
    Route::resource('menus.items', MenuItemController::class)
        ->parameters(['menus' => 'menu', 'items' => 'menuItem'])
        ->except(['show', 'index']);

    Route::get('newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])
        ->name('newsletter-subscribers.index');
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

Route::middleware('admin.permission:audit.view')->group(function (): void {
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
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
