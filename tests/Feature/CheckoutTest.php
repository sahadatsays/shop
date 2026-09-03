<?php

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('checkout page renders with cart summary', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->get(route('checkout'))
        ->assertSuccessful()
        ->assertSee('Checkout')
        ->assertSee('Shipping address')
        ->assertSee('Billing address')
        ->assertSee('Order summary')
        ->assertSee($product->name);
});

test('empty cart redirects away from checkout', function (): void {
    $this->get(route('checkout'))
        ->assertRedirect(route('cart'));
});

test('guest can place order with shipping billing and terms', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();
    $initialStock = $product->stock_quantity;

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertSuccessful();

    $response = $this->post(route('checkout.store'), [
        'email' => 'checkout-guest@example.com',
        'shipping' => [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'line1' => '123 Main Street',
            'line2' => 'Apt 4B',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
            'phone' => '555-0100',
        ],
        'billing_same_as_shipping' => '1',
        'delivery_method' => 'insideDhaka',
        'payment_method' => 'card',
        'terms_accepted' => '1',
    ]);

    $order = Order::query()->whereHas('customer', fn($q) => $q->where('email', 'checkout-guest@example.com'))->first();

    expect($order)->not->toBeNull()
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->items)->toHaveCount(1)
        ->and($order->shipping_address['city'])->toBe('Columbus')
        ->and($order->billing_address['city'])->toBe('Columbus');

    $response->assertRedirect(route('checkout.confirmation', $order));

    expect(CartItem::query()->count())->toBe(0);
    expect($product->fresh()->stock_quantity)->toBe($initialStock - 2);
});

test('place order requires terms acceptance', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->from(route('checkout'))
        ->post(route('checkout.store'), [
            'email' => 'terms-test@example.com',
            'shipping' => [
                'first_name' => 'Sam',
                'last_name' => 'Lee',
                'line1' => '456 Oak Ave',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78701',
                'country' => 'United States',
            ],
            'billing_same_as_shipping' => '1',
            'delivery_method' => 'insideDhaka',
            'payment_method' => 'card',
        ])
        ->assertRedirect(route('checkout'))
        ->assertSessionHasErrors('terms_accepted');

    expect(Order::query()->whereHas('customer', fn($q) => $q->where('email', 'terms-test@example.com'))->count())->toBe(0);
});

test('express shipping charge is applied to order total', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 10000,
    ]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'shipping-test@example.com',
        'shipping' => [
            'first_name' => 'Jordan',
            'last_name' => 'Reeves',
            'line1' => '789 Pine Rd',
            'city' => 'Denver',
            'state' => 'CO',
            'postal_code' => '80202',
            'country' => 'United States',
        ],
        'billing_same_as_shipping' => '1',
        'delivery_method' => 'outsideDhaka',
        'payment_method' => 'card',
        'terms_accepted' => '1',
    ])->assertRedirect();

    $order = Order::query()->latest('id')->first();

    expect($order->shipping_cents)->toBe(1200)
        ->and($order->total_cents)->toBe($order->subtotal_cents + 1200 + $order->tax_cents);
});

test('confirmation page shows order details after checkout', function (): void {
    $customer = Customer::factory()->create();
    session(['customer_id' => $customer->id, 'checkout_order_id' => null]);

    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => $customer->email,
        'shipping' => [
            'first_name' => 'Jordan',
            'last_name' => 'Reeves',
            'line1' => '100 Patriot Pkwy',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'billing_same_as_shipping' => '1',
        'delivery_method' => 'insideDhaka',
        'payment_method' => 'card',
        'terms_accepted' => '1',
    ]);

    $order = Order::query()->where('customer_id', $customer->id)->latest('id')->firstOrFail();

    session(['checkout_order_id' => $order->id]);

    $this->get(route('checkout.confirmation', $order))
        ->assertSuccessful()
        ->assertSee('Order confirmed')
        ->assertSee($order->order_number)
        ->assertSee($product->name);
});
