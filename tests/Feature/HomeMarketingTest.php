<?php

use App\Models\Collection;
use Database\Seeders\CommerceSeeder;
use Database\Seeders\HomepageSeeder;
use Database\Seeders\MarketingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
    $this->seed(MarketingSeeder::class);
    $this->seed(HomepageSeeder::class);
});

test('home page renders seeded hero banner and countdown promotion', function (): void {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Honor in every')
        ->assertSee('Weekend flash sale ends soon');
});

test('home page renders featured collections and best sellers from database', function (): void {
    $collection = Collection::query()->where('slug', 'expedition-collection')->firstOrFail();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee($collection->name, false)
        ->assertSee('Top Selling Products', false);
});

test('collection show page lists collection products', function (): void {
    $collection = Collection::query()
        ->where('slug', 'expedition-collection')
        ->with('products')
        ->firstOrFail();

    $this->get(route('collections.show', $collection))
        ->assertSuccessful()
        ->assertSee($collection->name)
        ->assertSee($collection->description);

    foreach ($collection->products->take(2) as $product) {
        $this->get(route('collections.show', $collection))->assertSee($product->name);
    }
});

test('inactive collection returns not found on storefront', function (): void {
    $collection = Collection::query()->where('slug', 'expedition-collection')->firstOrFail();
    $collection->update(['is_active' => false]);

    $this->get(route('collections.show', $collection))->assertNotFound();
});
