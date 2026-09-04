<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderTimelineEvent;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('orders index shows next status action for advanceable orders', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
        'placed_at' => now(),
    ]);

    $this->get(route('admin.orders.index', ['search' => $order->order_number]))
        ->assertSuccessful()
        ->assertSee('Next: Confirmed')
        ->assertSee('action="'.route('admin.orders.status.advance', $order).'"', false);
});

test('order status can be advanced to the next fulfillment step from the list', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    $this->from(route('admin.orders.index', ['status' => OrderStatus::Pending->value]))
        ->patch(route('admin.orders.status.advance', $order))
        ->assertRedirect(route('admin.orders.index', ['status' => OrderStatus::Pending->value]))
        ->assertSessionHas('success', 'Order advanced to Confirmed.');

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Confirmed)
        ->and(OrderTimelineEvent::query()->where('order_id', $order->id)->count())->toBe(1)
        ->and(OrderTimelineEvent::query()->where('order_id', $order->id)->value('message'))
        ->toBe('Status advanced to Confirmed.');
});

test('delivered and cancelled orders cannot be advanced', function (OrderStatus $status): void {
    $order = Order::factory()->create([
        'status' => $status,
    ]);

    $this->from(route('admin.orders.index'))
        ->patch(route('admin.orders.status.advance', $order))
        ->assertRedirect(route('admin.orders.index'))
        ->assertSessionHasErrors('status');

    expect($order->fresh()->status)->toBe($status);
})->with([
    'delivered' => OrderStatus::Delivered,
    'cancelled' => OrderStatus::Cancelled,
    'refunded' => OrderStatus::Refunded,
]);

test('admins without orders manage permission cannot advance status', function (): void {
    actingAsAdmin('inventory_manager');

    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    $this->patch(route('admin.orders.status.advance', $order))
        ->assertForbidden();

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});
