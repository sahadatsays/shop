<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimelineEvent;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Admin\InventoryService;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('customer can request a return on a delivered order', function (): void {
    $customer = actingAsCustomer();
    $product = Product::query()->published()->firstOrFail();
    $order = createDeliveredOrderForRefund($customer, $product);

    $this->post(route('account.orders.return', $order), [
        'reason' => 'The jacket was too large and I would like to exchange it for a smaller size.',
    ])->assertRedirect(route('account.orders.show', $order));

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Returned)
        ->and($order->return_requested_at)->not->toBeNull()
        ->and($order->return_reason)->toContain('too large');
});

test('customer cannot request a return on a pending order', function (): void {
    $customer = actingAsCustomer();
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Paid,
    ]);

    $this->post(route('account.orders.return', $order), [
        'reason' => 'Changed my mind about this purchase completely.',
    ])->assertSessionHasErrors('reason');
});

test('processing a refund restores product stock when sale movement exists', function (): void {
    actingAsAdmin();

    $product = Product::query()->published()->firstOrFail();
    $product->update(['stock_quantity' => 10]);

    $warehouse = Warehouse::query()->where('is_default', true)->firstOrFail();
    WarehouseStock::query()->updateOrCreate(
        ['product_id' => $product->id, 'warehouse_id' => $warehouse->id],
        ['quantity' => 10],
    );

    $stockBefore = 10;

    $order = Order::factory()->create([
        'status' => OrderStatus::Returned,
        'payment_status' => PaymentStatus::Paid,
        'total_cents' => 2000,
        'refunded_cents' => 0,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price_cents' => 1000,
        'line_total_cents' => 2000,
    ]);

    $inventory = app(InventoryService::class);

    $inventory->deductForSale(
        product: $product->fresh(),
        quantity: 2,
        reference: $order->order_number,
    );

    expect($product->fresh()->stock_quantity)->toBe($stockBefore - 2);

    $this->post(route('admin.orders.refunds.store', $order), [
        'refund_amount' => '20.00',
        'reason' => 'customer_return',
        'restore_stock' => true,
    ])->assertRedirect();

    expect($product->fresh()->stock_quantity)->toBe($stockBefore);
});

function createDeliveredOrderForRefund(Customer $customer, Product $product): Order
{
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'payment_status' => PaymentStatus::Paid,
        'total_cents' => $product->price_cents,
        'estimated_delivery_at' => now()->subDays(3),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price_cents' => $product->price_cents,
        'line_total_cents' => $product->price_cents,
    ]);

    OrderTimelineEvent::query()->create([
        'order_id' => $order->id,
        'status' => OrderStatus::Delivered,
        'message' => 'Package delivered.',
        'author_name' => 'System',
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ]);

    return $order;
}
