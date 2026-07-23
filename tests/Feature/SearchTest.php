<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Database\Seeders\HomepageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
    $this->seed(CommerceSeeder::class);
    $this->seed(HomepageSeeder::class);
});

test('search page returns matching products by name sku brand and category', function (): void {
    $category = Category::factory()->create(['name' => 'Searchable Category', 'slug' => 'searchable-category']);
    $brand = Brand::factory()->create(['name' => 'Searchable Brand', 'slug' => 'searchable-brand']);

    $product = Product::factory()->create([
        'name' => 'Unique Searchable Field Jacket',
        'sku' => 'SEARCH-SKU-999',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'stock_quantity' => 25,
    ]);

    $this->get(route('search', ['q' => 'Unique Searchable']))
        ->assertSuccessful()
        ->assertSee('Unique Searchable Field Jacket');

    $this->get(route('search', ['q' => 'SEARCH-SKU-999']))
        ->assertSuccessful()
        ->assertSee($product->name);

    $this->get(route('search', ['q' => 'Searchable Brand']))
        ->assertSuccessful()
        ->assertSee($product->name);

    $this->get(route('search', ['q' => 'Searchable Category']))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('search suggestions endpoint returns json matches', function (): void {
    Product::factory()->create([
        'name' => 'Suggestion Target Pack',
        'stock_quantity' => 10,
        'category_id' => Category::factory(),
    ]);

    $this->getJson(route('search.suggest', ['q' => 'Suggestion']))
        ->assertSuccessful()
        ->assertJsonStructure(['query', 'products', 'categories', 'brands'])
        ->assertJsonFragment(['name' => 'Suggestion Target Pack']);
});
