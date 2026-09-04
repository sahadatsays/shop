<?php

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderTimelineEvent;
use App\Models\Product;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('guest can look up an order with order number and email', function (): void {
    $order = Order::query()->with('customer')->has('customer')->firstOrFail();

    $this->post(route('track-order.store'), [
        'order_number' => $order->order_number,
        'email' => $order->customer->email,
    ])->assertRedirect(route('track-order.show', $order));

    $this->get(route('track-order.show', $order))
        ->assertSuccessful()
        ->assertSee($order->order_number)
        ->assertSee('Track your shipment');
});

test('guest lookup returns generic error for invalid credentials', function (): void {
    $this->post(route('track-order.store'), [
        'order_number' => 'VS-99999',
        'email' => 'wrong@example.com',
    ])->assertSessionHasErrors('order_number')
        ->assertSessionHasInput('order_number')
        ->assertSessionHasInput('email');
});

test('guest cannot view another customers order without lookup verification', function (): void {
    $order = Order::query()->with('customer')->firstOrFail();

    $this->get(route('track-order.show', $order))->assertNotFound();
});

test('logged in customer can view their order history and tracking page', function (): void {
    $customer = Customer::query()->has('orders')->firstOrFail();
    $order = $customer->orders()->latest('placed_at')->firstOrFail();

    actingAsCustomer($customer);

    $this->get(route('account.orders'))
        ->assertSuccessful()
        ->assertSee('Order history')
        ->assertSee($order->order_number);

    $this->get(route('account.orders.show', $order))
        ->assertSuccessful()
        ->assertSee($order->order_number)
        ->assertSee('Order details')
        ->assertSee('Account navigation', false)
        ->assertSee('Shipment progress')
        ->assertSee('Items in this order');
});

test('customer order details shows write review for delivered reviewable products', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();

    $order = createDeliveredOrderForCustomer($customer, $product);

    actingAsCustomer($customer);

    $this->get(route('account.orders.show', $order))
        ->assertSuccessful()
        ->assertSee('Write review')
        ->assertSee($product->name);
});

test('logged in customer cannot view another customers order', function (): void {
    $customer = Customer::query()->has('orders')->firstOrFail();
    $otherOrder = Order::query()
        ->where('customer_id', '!=', $customer->id)
        ->firstOrFail();

    actingAsCustomer($customer);

    $this->get(route('account.orders.show', $otherOrder))->assertNotFound();
});

test('admin status update creates timeline history and enforces transitions', function (): void {
    actingAsAdmin();

    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    $this->patch(route('admin.orders.status.update', $order), [
        'status' => OrderStatus::Confirmed->value,
        'message' => 'Payment verified manually.',
    ])->assertRedirect(route('admin.orders.show', $order));

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed)
        ->and(OrderTimelineEvent::query()->where('order_id', $order->id)->count())->toBe(1);

    $this->patch(route('admin.orders.status.update', $order), [
        'status' => OrderStatus::Pending->value,
    ])->assertSessionHasErrors('status');
});

test('cancelled orders cannot move back to processing', function (): void {
    actingAsAdmin();

    $order = Order::factory()->create([
        'status' => OrderStatus::Cancelled,
    ]);

    $this->patch(route('admin.orders.status.update', $order), [
        'status' => OrderStatus::Processing->value,
    ])->assertSessionHasErrors('status');
});

test('track order lookup page renders', function (): void {
    $this->get(route('track-order.create'))
        ->assertSuccessful()
        ->assertSee('Track your order')
        ->assertSee('Order number');
});
