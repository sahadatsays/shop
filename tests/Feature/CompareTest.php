<?php

use App\Models\CompareItem;
use App\Models\Customer;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('guest can add and remove compare items', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('compare.items.store'), [
        'product_id' => $product->id,
    ])->assertSuccessful()
        ->assertJsonPath('compare.item_count', 1)
        ->assertJsonPath('compare.product_ids.0', $product->id);

    $item = CompareItem::query()->where('product_id', $product->id)->firstOrFail();

    $this->deleteJson(route('compare.items.destroy', $item))
        ->assertSuccessful()
        ->assertJsonPath('compare.item_count', 0);
});

test('compare toggle adds and removes items', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('compare.toggle'), [
        'product_id' => $product->id,
    ])->assertSuccessful()
        ->assertJsonPath('in_compare', true)
        ->assertJsonPath('compare.item_count', 1);

    $this->postJson(route('compare.toggle'), [
        'product_id' => $product->id,
    ])->assertSuccessful()
        ->assertJsonPath('in_compare', false)
        ->assertJsonPath('compare.item_count', 0);
});

test('compare page renders saved products', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('compare.items.store'), [
        'product_id' => $product->id,
    ])->assertSuccessful();

    $this->get(route('compare'))
        ->assertSuccessful()
        ->assertSee('Compare products')
        ->assertSee($product->name);
});

test('compare list enforces maximum item limit', function (): void {
    $products = Product::query()->published()->inStock()->limit(5)->get();
    $maxItems = (int) config('compare.max_items', 4);

    foreach ($products->take($maxItems) as $product) {
        $this->postJson(route('compare.items.store'), [
            'product_id' => $product->id,
        ])->assertSuccessful();
    }

    $this->postJson(route('compare.items.store'), [
        'product_id' => $products->last()->id,
    ])->assertStatus(422);
});

test('customer login merges guest compare list into customer compare list', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $first = Product::query()->published()->inStock()->firstOrFail();
    $second = Product::query()->published()->inStock()->whereKeyNot($first->id)->firstOrFail();

    $this->postJson(route('compare.items.store'), ['product_id' => $first->id])->assertSuccessful();

    $this->postJson(route('login.store'), [
        'email' => $customer->email,
        'password' => 'password',
    ])->assertSuccessful();

    $this->postJson(route('compare.items.store'), ['product_id' => $second->id])->assertSuccessful();

    expect(CompareItem::query()->whereHas('compareList', fn ($query) => $query->where('customer_id', $customer->id))->count())
        ->toBe(2);
});

test('cannot add unpublished products to compare', function (): void {
    $product = Product::factory()->draft()->create(['stock_quantity' => 10]);

    $this->postJson(route('compare.items.store'), [
        'product_id' => $product->id,
    ])->assertStatus(422);
});

test('compare clear removes all items', function (): void {
    $products = Product::query()->published()->inStock()->limit(2)->get();

    foreach ($products as $product) {
        $this->postJson(route('compare.items.store'), ['product_id' => $product->id])->assertSuccessful();
    }

    $this->deleteJson(route('compare.clear'))
        ->assertSuccessful()
        ->assertJsonPath('compare.item_count', 0);
});
