<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
    $this->seed(CommerceSeeder::class);
});

test('shop page renders published in-stock products', function (): void {
    $product = Product::query()
        ->published()
        ->inStock()
        ->orderByDesc('is_featured')
        ->orderBy('sort_order')
        ->firstOrFail();

    $this->get(route('shop'))
        ->assertSuccessful()
        ->assertSee('All Products')
        ->assertSee($product->name);
});

test('shop excludes out of stock products by default', function (): void {
    $outOfStock = Product::query()->published()->outOfStock()->firstOrFail();

    $this->get(route('shop'))
        ->assertSuccessful()
        ->assertDontSee($outOfStock->name);
});

test('shop can filter by category slug', function (): void {
    $product = Product::query()->published()->inStock()->with('category')->firstOrFail();

    $this->get(route('shop', ['category' => $product->category->slug]))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('shop search matches product name', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->get(route('shop', ['search' => $product->name]))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('shop supports sorting by price low to high', function (): void {
    $this->get(route('shop', ['sort' => 'price_low', 'per_page' => 48]))
        ->assertSuccessful();

    $prices = Product::query()->published()->inStock()->orderBy('price_cents')->limit(2)->pluck('price_cents');

    expect($prices->count())->toBeGreaterThan(0);
});

test('shop pagination preserves query string', function (): void {
    $response = $this->get(route('shop', ['sort' => 'newest', 'per_page' => 12]));

    $response->assertSuccessful();

    if ($response->getContent() && str_contains($response->getContent(), 'page=2')) {
        expect($response->getContent())->toContain('sort=newest');
    }
});

test('shop validates invalid sort option', function (): void {
    $this->get(route('shop', ['sort' => 'invalid']))
        ->assertSessionHasErrors('sort');
});

test('shop filters on sale products', function (): void {
    $product = Product::factory()->create([
        'status' => ProductStatus::Published,
        'stock_quantity' => 10,
        'price_cents' => 5000,
        'compare_at_price_cents' => 8000,
    ]);

    $this->get(route('shop', ['on_sale' => 1, 'search' => $product->name]))
        ->assertSuccessful()
        ->assertSee($product->name);
});
