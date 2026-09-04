<?php

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('checkout accepts cash on delivery and keeps payment pending', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'cod-checkout@example.com',
        'shipping' => [
            'first_name' => 'Casey',
            'last_name' => 'Rivera',
            'line1' => '123 Main Street',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'delivery_method' => 'insideDhaka',
        'payment_method' => 'cod',
        'terms_accepted' => '1',
    ])->assertRedirect();

    $order = Order::query()->whereHas('customer', fn ($q) => $q->where('email', 'cod-checkout@example.com'))->firstOrFail();

    expect($order->payment_status)->toBe(PaymentStatus::Pending)
        ->and($order->payment_method)->toBe('Cash on delivery')
        ->and($order->payment_reference)->not->toBeNull()
        ->and($order->order_number)->toStartWith('VS-')
        ->and(strlen($order->order_number))->toBeGreaterThan(8);
});

test('checkout rejects online card payment while under construction', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->from(route('checkout'))
        ->post(route('checkout.store'), [
            'email' => 'card-blocked@example.com',
            'shipping' => [
                'first_name' => 'Casey',
                'last_name' => 'Rivera',
                'line1' => '123 Main Street',
                'city' => 'Columbus',
                'state' => 'OH',
                'postal_code' => '43215',
                'country' => 'United States',
            ],
            'delivery_method' => 'insideDhaka',
            'payment_method' => 'card',
            'terms_accepted' => '1',
        ])
        ->assertRedirect(route('checkout'))
        ->assertSessionHasErrors('payment_method');

    expect(Order::query()->whereHas('customer', fn ($q) => $q->where('email', 'card-blocked@example.com'))->exists())->toBeFalse();
});
