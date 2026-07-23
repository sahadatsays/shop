<?php

use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
    $this->seed(CommerceSeeder::class);
});

test('product show page renders without error', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee($product->name)
        ->assertSee('data-product-id="'.$product->id.'"', false);
});
