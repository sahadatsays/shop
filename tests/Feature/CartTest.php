<?php

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('guest can add items to cart and view cart page', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertSuccessful()
        ->assertJsonPath('cart.item_count', 2);

    $this->get(route('cart'))
        ->assertSuccessful()
        ->assertSee($product->name)
        ->assertSee('Your cart');
});

test('guest cart quantity can be updated and items removed', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $cartItem = CartItem::query()->where('product_id', $product->id)->firstOrFail();

    $this->patchJson(route('cart.items.update', $cartItem), [
        'quantity' => 3,
    ])->assertSuccessful()
        ->assertJsonPath('cart.item_count', 3);

    $this->deleteJson(route('cart.items.destroy', $cartItem))
        ->assertSuccessful()
        ->assertJsonPath('cart.item_count', 0);
});

test('cannot add unpublished or out of stock products', function (): void {
    $draft = Product::factory()->draft()->create(['stock_quantity' => 10]);
    $outOfStock = Product::factory()->outOfStock()->create(['status' => ProductStatus::Published]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $draft->id,
        'quantity' => 1,
    ])->assertStatus(422);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $outOfStock->id,
        'quantity' => 1,
    ])->assertStatus(422);
});

test('stock validation prevents exceeding available quantity', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 2,
    ]);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 5,
    ])->assertStatus(422);
});

test('customer login merges guest cart into customer cart', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $productA = Product::query()->published()->inStock()->skip(0)->firstOrFail();
    $productB = Product::query()->published()->inStock()->skip(1)->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $productA->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $guestCart = Cart::query()->whereNull('customer_id')->firstOrFail();
    expect($guestCart->items)->toHaveCount(1);

    $this->postJson(route('login.store'), [
        'email' => $customer->email,
    ])->assertSuccessful()
        ->assertJsonPath('customer.id', $customer->id);

    $customerCart = Cart::query()->where('customer_id', $customer->id)->firstOrFail();
    expect($customerCart->items)->toHaveCount(1)
        ->and(Cart::query()->whereNull('customer_id')->count())->toBe(0);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $productB->id,
        'quantity' => 2,
    ])->assertSuccessful();

    $customerCart->refresh()->load('items');
    expect($customerCart->items)->toHaveCount(2);
});

test('logged in customer can save cart', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->inStock()->firstOrFail();

    actingAsCustomer($customer);

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson(route('cart.save'))
        ->assertSuccessful()
        ->assertJsonPath('message', 'Cart saved for later.');

    expect(Cart::query()->where('customer_id', $customer->id)->first()?->is_saved)->toBeTrue();
});

test('checkout requires valid cart with stock', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->get(route('checkout'))
        ->assertSuccessful()
        ->assertSee($product->name);

    $this->get(route('cart'))->assertSuccessful();

    $cartItem = CartItem::query()->where('product_id', $product->id)->firstOrFail();
    $product->update(['stock_quantity' => 0]);

    $this->get(route('checkout'))
        ->assertRedirect(route('cart'));
});

test('cart validate endpoint confirms cart is ready', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->postJson(route('cart.validate'))
        ->assertSuccessful()
        ->assertJsonPath('valid', true);
});
