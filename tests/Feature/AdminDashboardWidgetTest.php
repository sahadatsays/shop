<?php

use App\Enums\Admin\DashboardDateRange;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\DashboardUserWidget;
use App\Models\DashboardWidget;
use App\Models\Order;
use App\Services\Admin\Dashboard\DashboardMetrics;
use App\Support\Dashboard\WidgetContext;
use Carbon\CarbonImmutable;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\DashboardWidgetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(DashboardWidgetSeeder::class);
});

test('widgets are gated by permission per role', function (): void {
    // Inventory manager: has inventory.view + products.view, but NOT orders.view.
    actingAsAdmin('inventory_manager');

    $response = $this->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('Low Stock Alerts');   // inventory.view
    $response->assertSee('Catalog Health');     // products.view
    $response->assertDontSee('Order Pipeline'); // orders.view — not permitted
    $response->assertDontSee('Recent Orders');
});

test('owner sees the full widget catalog', function (): void {
    actingAsAdmin('owner');

    $response = $this->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('Order Pipeline');
    $response->assertSee('Customer Snapshot');
    $response->assertSee('Marketing Calendar');
});

test('a permitted widget renders its body async', function (): void {
    actingAsAdmin('owner');

    $response = $this->get('/admin/dashboard/widgets/recent-orders?range=last_30_days');

    $response->assertSuccessful();
    $response->assertJsonStructure(['html']);
});

test('an unauthorized widget endpoint returns 404', function (): void {
    // Customer support lacks inventory.view — the low-stock widget must not resolve.
    actingAsAdmin('customer_support');

    $this->get('/admin/dashboard/widgets/low-stock?range=last_30_days')->assertNotFound();
});

test('users can save and reset widget preferences', function (): void {
    $user = actingAsAdmin('owner');
    $widget = DashboardWidget::where('key', 'sales-trend')->firstOrFail();

    $this->postJson(route('admin.dashboard.preferences.save'), [
        'widgets' => [
            ['key' => 'sales-trend', 'is_visible' => false, 'is_pinned' => true, 'position' => 0],
        ],
    ])->assertSuccessful();

    $preference = DashboardUserWidget::where('user_id', $user->id)
        ->where('dashboard_widget_id', $widget->id)
        ->first();

    expect($preference)->not->toBeNull();
    expect($preference->is_visible)->toBeFalse();
    expect($preference->is_pinned)->toBeTrue();

    $this->post(route('admin.dashboard.preferences.reset'))->assertRedirect();

    expect(DashboardUserWidget::where('user_id', $user->id)->count())->toBe(0);
});

test('hidden widgets are removed from the visible grid but tracked', function (): void {
    $user = actingAsAdmin('owner');
    $widget = DashboardWidget::where('key', 'quick-actions')->firstOrFail();

    DashboardUserWidget::create([
        'user_id' => $user->id,
        'dashboard_widget_id' => $widget->id,
        'is_visible' => false,
    ]);

    $response = $this->get('/admin');

    // The shell is still rendered (for client-side re-show) but marked hidden.
    $response->assertSee('data-visible="false"', false);
});

test('management index is permission gated', function (): void {
    actingAsAdmin('order_manager'); // no dashboard.widgets.view
    $this->get(route('admin.dashboard-widgets.index'))->assertForbidden();

    actingAsAdmin('owner');
    $this->get(route('admin.dashboard-widgets.index'))->assertSuccessful();
});

test('an admin can create, toggle, and delete a widget', function (): void {
    actingAsAdmin('owner');

    $this->post(route('admin.dashboard-widgets.store'), [
        'key' => 'custom-widget',
        'name' => 'Custom Widget',
        'type' => 'stat_group',
        'category' => 'general',
        'width' => 6,
        'height' => 1,
        'display_order' => 500,
        'is_active' => '1',
    ])->assertRedirect(route('admin.dashboard-widgets.index'));

    $widget = DashboardWidget::where('key', 'custom-widget')->firstOrFail();
    expect($widget->is_active)->toBeTrue();

    $this->patch(route('admin.dashboard-widgets.toggle', $widget))->assertRedirect();
    expect($widget->fresh()->is_active)->toBeFalse();

    $this->delete(route('admin.dashboard-widgets.destroy', $widget))->assertRedirect(route('admin.dashboard-widgets.index'));
    expect(DashboardWidget::where('key', 'custom-widget')->exists())->toBeFalse();
});

test('system widgets cannot be deleted', function (): void {
    actingAsAdmin('owner');
    $widget = DashboardWidget::where('key', 'sales-stats')->firstOrFail();

    $this->delete(route('admin.dashboard-widgets.destroy', $widget))->assertForbidden();
    expect(DashboardWidget::where('key', 'sales-stats')->exists())->toBeTrue();
});

test('revenue only counts completed (delivered) orders', function (): void {
    $customer = Customer::factory()->create();
    $now = CarbonImmutable::now();

    Order::factory()->for($customer)->create(['status' => OrderStatus::Delivered, 'total_cents' => 10000, 'placed_at' => $now]);
    Order::factory()->for($customer)->create(['status' => OrderStatus::Delivered, 'total_cents' => 5000, 'placed_at' => $now]);
    Order::factory()->for($customer)->create(['status' => OrderStatus::Pending, 'total_cents' => 9999, 'placed_at' => $now]);
    Order::factory()->for($customer)->create(['status' => OrderStatus::Cancelled, 'total_cents' => 8888, 'placed_at' => $now]);
    Order::factory()->for($customer)->create(['status' => OrderStatus::Returned, 'total_cents' => 7777, 'placed_at' => $now]);

    $metrics = app(DashboardMetrics::class);
    $context = WidgetContext::make(null, DashboardDateRange::ThisMonth);

    expect($metrics->completedRevenueCents($context->start, $context->end))->toBe(15000);
});

test('orders placed outside the range are excluded from revenue', function (): void {
    $customer = Customer::factory()->create();

    Order::factory()->for($customer)->create([
        'status' => OrderStatus::Delivered,
        'total_cents' => 20000,
        'placed_at' => CarbonImmutable::now()->subMonths(3),
    ]);

    $metrics = app(DashboardMetrics::class);
    $context = WidgetContext::make(null, DashboardDateRange::Today);

    expect($metrics->completedRevenueCents($context->start, $context->end))->toBe(0);
});
