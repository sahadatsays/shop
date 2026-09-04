<?php

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
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

test('admin can open create order page', function (): void {
    $this->get(route('admin.orders.create'))
        ->assertSuccessful()
        ->assertSee('Create order')
        ->assertSee('Products')
        ->assertSee('Shipping charge (BDT)')
        ->assertSee('Initial payment (BDT)');
});

test('admin can create an order with inventory deduction payment and invoice', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::query()->published()->inStock()->firstOrFail();
    $initialStock = $product->stock_quantity;
    $qty = min(2, $initialStock);
    $idempotencyKey = (string) Str::uuid();

    $response = $this->post(route('admin.orders.store'), [
        'customer_mode' => 'existing',
        'customer_id' => $customer->id,
        'source' => OrderSource::Phone->value,
        'shipping_method' => 'insideDhaka',
        'shipping_amount' => 60,
        'payment_method' => PaymentMethod::Cash->value,
        'initial_payment_amount' => 0,
        'idempotency_key' => $idempotencyKey,
        'shipping_address' => [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'line1' => 'House 12, Road 5',
            'city' => 'Dhaka',
            'state' => 'Dhaka',
            'postal_code' => '1209',
            'country' => 'Bangladesh',
            'phone' => '01712345678',
        ],
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => $qty,
            ],
        ],
    ]);

    $order = Order::query()->where('idempotency_key', $idempotencyKey)->first();

    expect($order)->not->toBeNull();

    $response->assertRedirect(route('admin.orders.show', $order));

    $order->refresh();
    $product->refresh();

    expect($order->source)->toBe(OrderSource::Phone)
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->payment_status)->toBe(PaymentStatus::Pending)
        ->and($order->shipping_cents)->toBe(6000)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->product_name)->toBe($product->name)
        ->and($product->stock_quantity)->toBe($initialStock - $qty)
        ->and(StockMovement::query()->where('reference', $order->order_number)->where('type', StockMovementType::Sale)->exists())->toBeTrue()
        ->and(Invoice::query()->where('order_id', $order->id)->exists())->toBeTrue();

    $this->get(route('admin.orders.invoice', $order))
        ->assertSuccessful()
        ->assertSee($order->invoice->invoice_number)
        ->assertSee($order->order_number);
});

test('duplicate idempotency key does not create a second order', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::query()->published()->inStock()->firstOrFail();
    $idempotencyKey = (string) Str::uuid();

    $payload = [
        'customer_mode' => 'existing',
        'customer_id' => $customer->id,
        'source' => OrderSource::Admin->value,
        'shipping_amount' => 0,
        'payment_method' => PaymentMethod::Cash->value,
        'initial_payment_amount' => 0,
        'idempotency_key' => $idempotencyKey,
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
    ];

    $this->post(route('admin.orders.store'), $payload)->assertRedirect();
    $this->post(route('admin.orders.store'), $payload)->assertRedirect();

    expect(Order::query()->where('idempotency_key', $idempotencyKey)->count())->toBe(1);
});

test('admin cannot create an order exceeding available stock', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->from(route('admin.orders.create'))
        ->post(route('admin.orders.store'), [
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'source' => OrderSource::Admin->value,
            'shipping_amount' => 0,
            'payment_method' => PaymentMethod::Cash->value,
            'initial_payment_amount' => 0,
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
                [
                    'product_id' => $product->id,
                    'quantity' => $product->stock_quantity + 5,
                ],
            ],
        ])
        ->assertRedirect(route('admin.orders.create'))
        ->assertSessionHasErrors();
});
