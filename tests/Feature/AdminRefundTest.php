<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\RefundReason;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Models\Refund;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('refunds index page renders', function (): void {
    $this->get(route('admin.refunds.index'))
        ->assertSuccessful()
        ->assertSee('Refunds');
});

test('admin can process a full refund and update payment status', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Cancelled,
        'payment_status' => PaymentStatus::Paid,
        'total_cents' => 5000,
        'paid_cents' => 5000,
        'refunded_cents' => 0,
    ]);

    $this->post(route('admin.orders.refunds.store', $order), [
        'refund_amount' => '50.00',
        'reason' => RefundReason::OrderCancelled->value,
        'notes' => 'Customer cancelled before shipment.',
        'restore_stock' => true,
    ])->assertRedirect();

    $order->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::Refunded)
        ->and($order->status)->toBe(OrderStatus::Refunded)
        ->and($order->refunded_cents)->toBe(5000);

    $this->assertDatabaseHas('refunds', [
        'order_id' => $order->id,
        'amount_cents' => 5000,
        'status' => RefundStatus::Completed->value,
    ]);
});

test('refund amount cannot exceed remaining balance', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Returned,
        'payment_status' => PaymentStatus::Paid,
        'total_cents' => 3000,
        'paid_cents' => 3000,
        'refunded_cents' => 1000,
    ]);

    $this->from(route('admin.orders.show', $order))
        ->post(route('admin.orders.refunds.store', $order), [
            'refund_amount' => '30.00',
            'reason' => RefundReason::CustomerReturn->value,
        ])->assertSessionHasErrors('amount_cents');
});

test('refund show page renders', function (): void {
    $order = Order::factory()->create(['total_cents' => 2500]);
    $refund = Refund::query()->create([
        'order_id' => $order->id,
        'amount_cents' => 2500,
        'reason' => RefundReason::CustomerReturn,
        'status' => RefundStatus::Completed,
        'restore_stock' => true,
        'payment_reference' => 'RF-TEST123',
        'processed_at' => now(),
    ]);

    $this->get(route('admin.refunds.show', $refund))
        ->assertSuccessful()
        ->assertSee('RF-TEST123')
        ->assertSee($order->order_number);
});
