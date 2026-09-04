<?php

use App\Enums\DiscountType;
use App\Enums\ProductStatus;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Database\Seeders\MarketingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
    $this->seed(MarketingSeeder::class);
});

test('guest can apply valid coupon to cart', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 10000,
    ]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson(route('cart.coupon.apply'), ['code' => 'VALOR10'])
        ->assertSuccessful()
        ->assertJsonPath('cart.coupon_code', 'VALOR10')
        ->assertJsonPath('cart.discount_cents', 1000);
});

test('invalid coupon returns error', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson(route('cart.coupon.apply'), ['code' => 'NOTREAL'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'That coupon code is not valid.');
});

test('coupon below minimum order is rejected', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 2000,
    ]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $response = $this->postJson(route('cart.coupon.apply'), ['code' => 'VALOR10']);

    $response->assertUnprocessable();
    expect($response->json('message'))->toContain('at least');
});

test('guest can remove applied coupon', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 10000,
    ]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson(route('cart.coupon.apply'), ['code' => 'VALOR10'])->assertSuccessful();

    $this->deleteJson(route('cart.coupon.remove'))
        ->assertSuccessful()
        ->assertJsonPath('cart.discount_cents', 0)
        ->assertJsonPath('cart.coupon_code', null);
});

test('checkout applies coupon discount to order total', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 10000,
    ]);

    $discount = Discount::query()->where('code', 'VALOR10')->firstOrFail();
    $initialUses = $discount->used_count;

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson(route('cart.coupon.apply'), ['code' => 'VALOR10'])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'coupon-checkout@example.com',
        'shipping' => [
            'first_name' => 'Casey',
            'last_name' => 'Morgan',
            'line1' => '123 Main Street',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'delivery_method' => 'standard',
        'payment_method' => 'cod',
        'terms_accepted' => '1',
    ])->assertRedirect();

    $order = Order::query()->whereHas('customer', fn ($q) => $q->where('email', 'coupon-checkout@example.com'))->firstOrFail();

    expect($order->subtotal_cents)->toBe(10000)
        ->and($order->discount_cents)->toBe(1000)
        ->and($order->coupon_code)->toBe('VALOR10')
        ->and($order->discount_id)->toBe($discount->id)
        ->and($order->total_cents)->toBe($order->subtotal_cents - $order->discount_cents + $order->shipping_cents + $order->tax_cents);

    expect($discount->fresh()->used_count)->toBe($initialUses + 1);
});

test('fixed amount coupon applies correct discount', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 10000,
    ]);

    Discount::factory()->create([
        'code' => 'SAVE15',
        'type' => DiscountType::Fixed,
        'value' => 1500,
        'min_order_cents' => null,
    ]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson(route('cart.coupon.apply'), ['code' => 'SAVE15'])
        ->assertSuccessful()
        ->assertJsonPath('cart.discount_cents', 1500);
});
