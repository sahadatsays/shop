<?php

use App\Enums\AuditAction;
use App\Enums\AuditCategory;
use App\Enums\CustomerStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('audit logs page renders for authorized admins', function (): void {
    actingAsAdmin();

    AuditLog::query()->create([
        'action' => AuditAction::AdminLogin,
        'category' => AuditCategory::Auth,
        'description' => 'Jordan Reeves signed in to the admin panel.',
        'causer_type' => User::class,
        'causer_id' => User::query()->where('email', 'owner@valorsupply.co')->value('id'),
        'properties' => ['email' => 'owner@valorsupply.co'],
        'ip_address' => '127.0.0.1',
        'browser' => 'Chrome',
    ]);

    $this->get(route('admin.audit-logs.index', ['search' => 'Jordan Reeves']))
        ->assertSuccessful()
        ->assertSee('Audit logs')
        ->assertSee('Jordan Reeves signed in to the admin panel.')
        ->assertSee('127.0.0.1')
        ->assertSee('Chrome');
});

test('audit logs can be filtered by category and search', function (): void {
    actingAsAdmin();

    AuditLog::query()->create([
        'action' => AuditAction::StockChanged,
        'category' => AuditCategory::Inventory,
        'description' => 'Stock for Heritage Wool Beanie changed from 10 to 15.',
        'ip_address' => '10.0.0.5',
        'browser' => 'Safari',
    ]);

    AuditLog::query()->create([
        'action' => AuditAction::AdminLogin,
        'category' => AuditCategory::Auth,
        'description' => 'Jordan Reeves signed in to the admin panel.',
        'ip_address' => '127.0.0.1',
        'browser' => 'Chrome',
    ]);

    $this->get(route('admin.audit-logs.index', ['category' => AuditCategory::Inventory->value]))
        ->assertSuccessful()
        ->assertSee('Stock for Heritage Wool Beanie changed from 10 to 15.')
        ->assertDontSee('Jordan Reeves signed in to the admin panel.');

    $this->get(route('admin.audit-logs.index', ['search' => '10.0.0.5']))
        ->assertSuccessful()
        ->assertSee('Stock for Heritage Wool Beanie changed from 10 to 15.')
        ->assertDontSee('Jordan Reeves signed in to the admin panel.');
});

test('admin without audit permission cannot view audit logs', function (): void {
    actingAsAdmin('product_manager');

    $this->get(route('admin.audit-logs.index'))->assertForbidden();
});

test('successful admin login is recorded in audit logs', function (): void {
    $this->post(route('admin.login.store'), [
        'email' => 'owner@valorsupply.co',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $log = AuditLog::query()->where('action', AuditAction::AdminLogin)->first();

    expect($log)->not->toBeNull()
        ->and($log->category)->toBe(AuditCategory::Auth)
        ->and($log->causer)->toBeInstanceOf(User::class)
        ->and($log->ip_address)->not->toBeNull();
});

test('failed admin login is recorded in audit logs', function (): void {
    $this->post(route('admin.login.store'), [
        'email' => 'owner@valorsupply.co',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $log = AuditLog::query()->where('action', AuditAction::AdminLoginFailed)->first();

    expect($log)->not->toBeNull()
        ->and($log->category)->toBe(AuditCategory::Auth)
        ->and($log->properties['email'])->toBe('owner@valorsupply.co');
});

test('admin logout is recorded in audit logs', function (): void {
    actingAsAdmin();

    $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));

    expect(AuditLog::query()->where('action', AuditAction::AdminLogout)->exists())->toBeTrue();
});

test('customer login and logout are recorded in audit logs', function (): void {
    $customer = Customer::factory()->create([
        'status' => CustomerStatus::Active,
    ]);

    $this->post(route('login.store'), [
        'email' => $customer->email,
    ])->assertRedirect(route('account'));

    expect(AuditLog::query()->where('action', AuditAction::CustomerLogin)->exists())->toBeTrue();

    actingAsCustomer($customer);

    $this->post(route('logout'))->assertRedirect(route('home'));

    expect(AuditLog::query()->where('action', AuditAction::CustomerLogout)->exists())->toBeTrue();
});

test('product updates are recorded in audit logs', function (): void {
    actingAsAdmin();

    $product = Product::query()->firstOrFail();

    $this->patch(route('admin.products.update', $product), [
        'name' => 'Updated Audit Product',
        'sku' => $product->sku,
        'category_id' => $product->category_id,
        'price' => number_format($product->price_cents / 100, 2, '.', ''),
        'stock_quantity' => $product->stock_quantity,
        'low_stock_threshold' => $product->low_stock_threshold,
        'status' => ProductStatus::Published->value,
    ])->assertRedirect(route('admin.products.index'));

    $log = AuditLog::query()->where('action', AuditAction::ProductUpdated)->latest('id')->first();

    expect($log)->not->toBeNull()
        ->and($log->subject?->is($product))->toBeTrue()
        ->and($log->properties['changes']['name']['to'])->toBe('Updated Audit Product');
});

test('order status updates are recorded in audit logs', function (): void {
    actingAsAdmin();

    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    $this->patch(route('admin.orders.status.update', $order), [
        'status' => OrderStatus::Confirmed->value,
        'message' => 'Payment verified manually.',
    ])->assertRedirect(route('admin.orders.show', $order));

    $log = AuditLog::query()->where('action', AuditAction::OrderStatusUpdated)->latest('id')->first();

    expect($log)->not->toBeNull()
        ->and($log->subject?->is($order))->toBeTrue()
        ->and($log->properties['status'])->toBe(OrderStatus::Confirmed->value);
});

test('creating a product records product and stock audit logs', function (): void {
    actingAsAdmin();

    $category = Category::query()->firstOrFail();

    $this->post(route('admin.products.store'), [
        'name' => 'Audit Trail Pack',
        'sku' => 'PKT-AUD-001',
        'category_id' => $category->id,
        'price' => '49.99',
        'stock_quantity' => 12,
        'low_stock_threshold' => 3,
        'status' => ProductStatus::Published->value,
    ])->assertRedirect(route('admin.products.index'));

    expect(AuditLog::query()->where('action', AuditAction::ProductCreated)->exists())->toBeTrue()
        ->and(AuditLog::query()->where('action', AuditAction::StockChanged)->exists())->toBeTrue();
});
