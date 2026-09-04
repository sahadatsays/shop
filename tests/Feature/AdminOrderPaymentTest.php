<?php

use App\Enums\OrderSource;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('admin can record partial and completing payments on an order', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->post(route('admin.orders.store'), [
        'customer_mode' => 'existing',
        'customer_id' => $customer->id,
        'source' => OrderSource::Admin->value,
        'shipping_cents' => 0,
        'payment_method' => PaymentMethod::Cash->value,
        'initial_payment_cents' => 0,
        'idempotency_key' => (string) Str::uuid(),
        'shipping_address' => [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'line1' => '123 Main St',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ])->assertRedirect();

    $order = Order::query()->latest('id')->firstOrFail();
    $half = (int) floor($order->total_cents / 2);

    $this->post(route('admin.orders.payments.store', $order), [
        'amount' => number_format($half / 100, 2, '.', ''),
        'method' => PaymentMethod::Cash->value,
        'transaction_reference' => 'CASH-1',
    ])->assertRedirect(route('admin.orders.show', $order));

    $order->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::PartiallyPaid)
        ->and($order->paid_cents)->toBe($half)
        ->and($order->dueCents())->toBe($order->total_cents - $half)
        ->and(Payment::query()->where('order_id', $order->id)->count())->toBe(1);

    $this->post(route('admin.orders.payments.store', $order), [
        'amount' => number_format($order->dueCents() / 100, 2, '.', ''),
        'method' => PaymentMethod::BankTransfer->value,
        'transaction_reference' => 'BANK-2',
    ])->assertRedirect(route('admin.orders.show', $order));

    $order->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->paid_cents)->toBe($order->total_cents)
        ->and($order->dueCents())->toBe(0)
        ->and(Payment::query()->where('order_id', $order->id)->count())->toBe(2);
});

test('payment amount cannot exceed remaining due balance', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->post(route('admin.orders.store'), [
        'customer_mode' => 'existing',
        'customer_id' => $customer->id,
        'source' => OrderSource::Admin->value,
        'shipping_cents' => 0,
        'payment_method' => PaymentMethod::Cash->value,
        'initial_payment_cents' => 0,
        'idempotency_key' => (string) Str::uuid(),
        'shipping_address' => [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'line1' => '123 Main St',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ])->assertRedirect();

    $order = Order::query()->latest('id')->firstOrFail();

    $this->from(route('admin.orders.show', $order))
        ->post(route('admin.orders.payments.store', $order), [
            'amount' => number_format(($order->total_cents + 500) / 100, 2, '.', ''),
            'method' => PaymentMethod::Cash->value,
        ])
        ->assertRedirect(route('admin.orders.show', $order))
        ->assertSessionHasErrors('amount_cents');
});
