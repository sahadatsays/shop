<?php

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('products index page renders with catalog data', function (): void {
    $listedProduct = Product::query()->ordered()->paginate(15)->firstOrFail();

    $this->get(route('admin.products.index'))
        ->assertSuccessful()
        ->assertSee('Products')
        ->assertSee($listedProduct->name)
        ->assertSee($listedProduct->sku);
});

test('product can be created with slug generation and catalog fields', function (): void {
    $category = Category::query()->firstOrFail();

    $this->post(route('admin.products.store'), [
        'name' => 'Operator Field Pack',
        'sku' => 'PKT-OFP-001',
        'barcode' => '1234567890123',
        'category_id' => $category->id,
        'price' => '89.99',
        'stock_quantity' => 25,
        'low_stock_threshold' => 5,
        'status' => ProductStatus::Published->value,
        'is_featured' => true,
        'is_new_arrival' => true,
        'short_description' => 'Durable field pack.',
        'description' => 'Built for long-range carry.',
        'specifications' => [
            ['name' => 'Capacity', 'value' => '35L'],
        ],
        'attributes' => [
            ['name' => 'Color', 'value' => 'Coyote Brown'],
        ],
    ])->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('sku', 'PKT-OFP-001')->firstOrFail();

    expect($product->slug)->toBe('operator-field-pack')
        ->and($product->status)->toBe(ProductStatus::Published)
        ->and($product->price_cents)->toBe(8999)
        ->and($product->is_featured)->toBeTrue()
        ->and($product->is_new_arrival)->toBeTrue()
        ->and($product->specifications)->toHaveCount(1)
        ->and($product->attributes)->toHaveCount(1);
});

test('product can be updated with gallery and seo fields', function (): void {
    $product = Product::factory()->create([
        'name' => 'Old Product',
        'slug' => 'old-product',
        'sku' => 'OLD-001',
    ]);

    $related = Product::factory()->create(['sku' => 'REL-001']);

    $this->put(route('admin.products.update', $product), [
        'name' => 'Updated Product',
        'slug' => 'updated-product',
        'sku' => 'UPD-001',
        'category_id' => $product->category_id,
        'price' => '120.50',
        'stock_quantity' => 10,
        'status' => ProductStatus::Draft->value,
        'meta_title' => 'Product SEO',
        'meta_description' => 'Product description for search.',
        'meta_keywords' => 'product, gear',
        'related_product_ids' => [$related->id],
        'gallery' => [UploadedFile::fake()->image('product.jpg')],
    ])->assertRedirect(route('admin.products.index'));

    $product->refresh()->load(['images', 'relatedProducts']);

    expect($product->name)->toBe('Updated Product')
        ->and($product->slug)->toBe('updated-product')
        ->and($product->status)->toBe(ProductStatus::Draft)
        ->and($product->meta_title)->toBe('Product SEO')
        ->and($product->images)->toHaveCount(1)
        ->and($product->relatedProducts)->toHaveCount(1);
});

test('product can be soft deleted and restored', function (): void {
    $product = Product::factory()->create();

    $this->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);

    $this->post(route('admin.products.restore', $product->id))
        ->assertRedirect(route('admin.products.index', ['trashed' => 1]));

    expect(Product::query()->find($product->id))->not->toBeNull();
});

test('product show page renders', function (): void {
    $product = Product::query()->firstOrFail();

    $this->get(route('admin.products.show', $product))
        ->assertSuccessful()
        ->assertSee($product->name)
        ->assertSee($product->sku)
        ->assertSee('Edit product');
});

test('products nav link is enabled', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Products');
});
