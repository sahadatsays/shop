<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\WishlistItem;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('guest can add and remove wishlist items', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('wishlist.items.store'), [
        'product_id' => $product->id,
    ])->assertSuccessful()
        ->assertJsonPath('wishlist.item_count', 1)
        ->assertJsonPath('wishlist.product_ids.0', $product->id);

    $item = WishlistItem::query()->where('product_id', $product->id)->firstOrFail();

    $this->deleteJson(route('wishlist.items.destroy', $item))
        ->assertSuccessful()
        ->assertJsonPath('wishlist.item_count', 0);
});

test('wishlist toggle adds and removes items', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('wishlist.toggle'), [
        'product_id' => $product->id,
    ])->assertSuccessful()
        ->assertJsonPath('in_wishlist', true)
        ->assertJsonPath('wishlist.item_count', 1);

    $this->postJson(route('wishlist.toggle'), [
        'product_id' => $product->id,
    ])->assertSuccessful()
        ->assertJsonPath('in_wishlist', false)
        ->assertJsonPath('wishlist.item_count', 0);
});

test('wishlist page renders saved products', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('wishlist.items.store'), [
        'product_id' => $product->id,
    ])->assertSuccessful();

    $this->get(route('wishlist'))
        ->assertSuccessful()
        ->assertSee('Your wishlist')
        ->assertSee($product->name);
});

test('move wishlist item to cart removes it from wishlist and adds to cart', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('wishlist.items.store'), [
        'product_id' => $product->id,
    ])->assertSuccessful();

    $item = WishlistItem::query()->where('product_id', $product->id)->firstOrFail();

    $this->postJson(route('wishlist.items.move-to-cart', $item))
        ->assertSuccessful()
        ->assertJsonPath('wishlist.item_count', 0)
        ->assertJsonPath('cart.item_count', 1);
});

test('customer login merges guest wishlist into customer wishlist', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $first = Product::query()->published()->inStock()->firstOrFail();
    $second = Product::query()->published()->inStock()->whereKeyNot($first->id)->firstOrFail();

    $this->postJson(route('wishlist.items.store'), ['product_id' => $first->id])->assertSuccessful();

    $this->post(route('login.store'), [
        'email' => $customer->email,
    ])->assertRedirect(route('account'));

    $this->postJson(route('wishlist.items.store'), ['product_id' => $second->id])->assertSuccessful();

    expect(WishlistItem::query()->whereHas('wishlist', fn ($query) => $query->where('customer_id', $customer->id))->count())
        ->toBe(2);
});

test('cannot add unpublished products to wishlist', function (): void {
    $product = Product::factory()->draft()->create(['stock_quantity' => 10]);

    $this->postJson(route('wishlist.items.store'), [
        'product_id' => $product->id,
    ])->assertStatus(422);
});

test('wishlist clear removes all items', function (): void {
    $products = Product::query()->published()->inStock()->limit(2)->get();

    foreach ($products as $product) {
        $this->postJson(route('wishlist.items.store'), ['product_id' => $product->id])->assertSuccessful();
    }

    $this->deleteJson(route('wishlist.clear'))
        ->assertSuccessful()
        ->assertJsonPath('wishlist.item_count', 0);
});
