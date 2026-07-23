<?php

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
    $this->seed(CommerceSeeder::class);
});

test('product show page renders dynamic product data', function (): void {
    $product = Product::query()->where('slug', 'garrison-heritage-tee')->firstOrFail();

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee($product->name)
        ->assertSee($product->brand->name)
        ->assertSee($product->formattedPrice())
        ->assertSee($product->sku)
        ->assertSee('Shop', false)
        ->assertSee($product->category->name)
        ->assertSee('data-product-id="'.$product->id.'"', false);
});

test('product show page renders gallery specifications and variant options', function (): void {
    $product = Product::query()->where('slug', 'ranger-field-jacket')->firstOrFail();

    expect($product->images)->toHaveCount(4)
        ->and($product->specifications)->not->toBeEmpty()
        ->and($product->colorAttributes())->not->toBeEmpty()
        ->and($product->sizeAttributes())->not->toBeEmpty();

    $response = $this->get(route('product.show', $product));

    $response->assertSuccessful()
        ->assertSee('data-thumb="0"', false)
        ->assertSee('Weight')
        ->assertSee('Olive Drab')
        ->assertSee('Save $60.00');
});

test('product show page renders related products from database', function (): void {
    $product = Product::query()->where('slug', 'ranger-field-jacket')->firstOrFail();

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee('Patriot Canvas Rucksack')
        ->assertSee('Completes the kit');
});

test('product show page tracks recently viewed products in session', function (): void {
    $first = Product::query()->where('slug', 'garrison-heritage-tee')->firstOrFail();
    $second = Product::query()->where('slug', 'ranger-field-jacket')->firstOrFail();

    $this->get(route('product.show', $first))->assertSuccessful();
    $this->get(route('product.show', $second))
        ->assertSuccessful()
        ->assertSee('Recently viewed')
        ->assertSee($first->name);
});

test('product show falls back to category siblings when no related products configured', function (): void {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    ProductImage::query()->create([
        'product_id' => $product->id,
        'path' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800',
        'alt_text' => $product->name,
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $sibling = Product::factory()->create([
        'category_id' => $product->category_id,
        'stock_quantity' => 10,
    ]);

    ProductImage::query()->create([
        'product_id' => $sibling->id,
        'path' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800',
        'alt_text' => $sibling->name,
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee($sibling->name);
});

test('product show renders accordion sections from product attributes', function (): void {
    $product = Product::factory()->create([
        'stock_quantity' => 10,
        'description' => "First paragraph.\n\nSecond paragraph.",
    ]);

    ProductSpecification::query()->create([
        'product_id' => $product->id,
        'name' => 'Fabric weight',
        'value' => '12 oz',
        'sort_order' => 0,
    ]);

    ProductAttribute::query()->create([
        'product_id' => $product->id,
        'name' => 'Material',
        'value' => 'Waxed canvas shell',
        'sort_order' => 0,
    ]);

    ProductAttribute::query()->create([
        'product_id' => $product->id,
        'name' => 'Care',
        'value' => 'Spot clean only',
        'sort_order' => 1,
    ]);

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee('First paragraph.')
        ->assertSee('Fabric weight')
        ->assertSee('Waxed canvas shell')
        ->assertSee('Spot clean only');
});
