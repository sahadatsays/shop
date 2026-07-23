<?php

use App\Enums\DiscountType;
use App\Models\Collection;
use App\Models\Discount;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Promotion;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Database\Seeders\MarketingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('discounts index page renders for authorized admin', function (): void {
    $discount = Discount::factory()->create([
        'code' => 'TESTSAVE',
        'name' => 'Test Save Discount',
    ]);

    $this->get(route('admin.discounts.index'))
        ->assertSuccessful()
        ->assertSee('Discounts')
        ->assertSee($discount->code)
        ->assertSee($discount->name);
});

test('discount can be created updated and deleted', function (): void {
    $this->post(route('admin.discounts.store'), [
        'code' => 'WELCOME15',
        'name' => 'Welcome 15',
        'description' => 'Fifteen percent off first order.',
        'type' => DiscountType::Percent->value,
        'value' => 15,
        'min_order' => 25,
        'max_uses' => 500,
        'is_active' => true,
    ])->assertRedirect(route('admin.discounts.index'));

    $discount = Discount::query()->where('code', 'WELCOME15')->firstOrFail();

    $this->patch(route('admin.discounts.update', $discount), [
        'code' => 'WELCOME15',
        'name' => 'Welcome 15 Updated',
        'description' => 'Updated welcome discount.',
        'type' => DiscountType::Percent->value,
        'value' => 20,
        'min_order' => 30,
        'max_uses' => 500,
        'is_active' => true,
    ])->assertRedirect(route('admin.discounts.index'));

    expect($discount->fresh()->name)->toBe('Welcome 15 Updated')
        ->and($discount->fresh()->value)->toBe(20);

    $this->delete(route('admin.discounts.destroy', $discount))
        ->assertRedirect(route('admin.discounts.index'));

    expect(Discount::query()->whereKey($discount->id)->exists())->toBeFalse();
});

test('offers and collections admin pages render', function (): void {
    $this->seed(MarketingSeeder::class);

    $offer = Offer::query()->where('slug', 'spring-field-gear')->firstOrFail();
    $collection = Collection::query()->where('slug', 'expedition-collection')->firstOrFail();

    $this->get(route('admin.offers.index'))
        ->assertSuccessful()
        ->assertSee($offer->name);

    $this->get(route('admin.offers.show', $offer))
        ->assertSuccessful()
        ->assertSee($offer->headline);

    $this->get(route('admin.collections.index'))
        ->assertSuccessful()
        ->assertSee($collection->name);

    $this->get(route('admin.collections.show', $collection))
        ->assertSuccessful()
        ->assertSee($collection->description);
});

test('sale products page allows updating compare at price', function (): void {
    $product = Product::query()->published()->onSale()->firstOrFail();

    $this->get(route('admin.sale-products.index'))
        ->assertSuccessful()
        ->assertSee($product->name);

    $this->patch(route('admin.sale-products.update', $product), [
        'price' => number_format($product->price_cents / 100, 2, '.', ''),
        'compare_at_price' => number_format(($product->price_cents + 2500) / 100, 2, '.', ''),
    ])->assertRedirect(route('admin.sale-products.index'));

    expect($product->fresh()->compare_at_price_cents)->toBe($product->price_cents + 2500);
});

test('banner and countdown promotion admin pages render', function (): void {
    $this->seed(MarketingSeeder::class);

    $banner = Promotion::query()->where('slug', 'home-hero-spring-event')->firstOrFail();
    $countdown = Promotion::query()->where('slug', 'weekend-flash-sale')->firstOrFail();

    $this->get(route('admin.banner-promotions.index'))
        ->assertSuccessful()
        ->assertSee($banner->name);

    $this->get(route('admin.countdown-promotions.index'))
        ->assertSuccessful()
        ->assertSee($countdown->name);
});

test('marketing admin routes require authentication', function (): void {
    auth('admin')->logout();

    $this->get(route('admin.discounts.index'))->assertRedirect(route('admin.login'));
});
