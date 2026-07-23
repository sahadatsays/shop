<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\OrderTimelineEvent;
use App\Support\OrderNumberGenerator;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('orders index page renders with search and status filters', function (): void {
    $order = Order::query()->with('customer')->latest('placed_at')->firstOrFail();

    $this->get(route('admin.orders.index'))
        ->assertSuccessful()
        ->assertSee('Orders')
        ->assertSee($order->order_number);

    $this->get(route('admin.orders.index', ['search' => $order->order_number]))
        ->assertSuccessful()
        ->assertSee($order->order_number)
        ->assertSee($order->customer->name);

    $this->get(route('admin.orders.index', ['status' => OrderStatus::Pending->value]))
        ->assertSuccessful();
});

test('order show page displays summary items and timeline', function (): void {
    $order = Order::query()->with(['customer', 'items', 'timelineEvents'])->has('items')->firstOrFail();

    $this->get(route('admin.orders.show', $order))
        ->assertSuccessful()
        ->assertSee($order->order_number)
        ->assertSee($order->customer->name)
        ->assertSee('Order timeline')
        ->assertSee('Line items')
        ->assertSee('Update status');
});

test('order status can be updated and recorded on timeline', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    $this->patch(route('admin.orders.status.update', $order), [
        'status' => OrderStatus::Confirmed->value,
        'message' => 'Payment verified manually.',
    ])->assertRedirect(route('admin.orders.show', $order));

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Confirmed)
        ->and(OrderTimelineEvent::query()->where('order_id', $order->id)->count())->toBe(1)
        ->and(OrderTimelineEvent::query()->where('order_id', $order->id)->first()->message)
        ->toBe('Payment verified manually.');
});

test('admin note can be added to order', function (): void {
    $order = Order::factory()->create();

    $this->post(route('admin.orders.notes.store', $order), [
        'body' => 'Customer requested gift wrap.',
    ])->assertRedirect(route('admin.orders.show', $order));

    expect(OrderNote::query()->where('order_id', $order->id)->count())->toBe(1)
        ->and(OrderNote::query()->where('order_id', $order->id)->first()->body)
        ->toBe('Customer requested gift wrap.');
});

test('order number generator produces unique VS prefixed numbers', function (): void {
    $first = OrderNumberGenerator::generate();
    $second = OrderNumberGenerator::generate();

    expect($first)->toStartWith('VS-')
        ->and($second)->toStartWith('VS-')
        ->and($first)->not->toBe($second);
});
