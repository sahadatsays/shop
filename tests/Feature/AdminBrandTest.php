<?php

use App\Enums\BrandStatus;
use App\Models\Brand;
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

test('brands index page renders with product counts', function (): void {
    $brand = Brand::query()->withCount('products')->first();

    $this->get(route('admin.brands.index'))
        ->assertSuccessful()
        ->assertSee('Brands')
        ->assertSee($brand->name)
        ->assertSee((string) $brand->products_count);
});

test('brand can be created with slug generation and featured flag', function (): void {
    $this->post(route('admin.brands.store'), [
        'name' => 'Heritage Co',
        'status' => BrandStatus::Active->value,
        'is_featured' => true,
        'sort_order' => 3,
        'description' => 'Heritage gear for veterans.',
    ])->assertRedirect(route('admin.brands.index'));

    $this->assertDatabaseHas('brands', [
        'name' => 'Heritage Co',
        'slug' => 'heritage-co',
        'status' => BrandStatus::Active->value,
        'is_featured' => true,
    ]);
});

test('brand can be updated with logo and seo fields', function (): void {
    $brand = Brand::factory()->create(['name' => 'Old Brand', 'slug' => 'old-brand']);

    $this->put(route('admin.brands.update', $brand), [
        'name' => 'Updated Brand',
        'slug' => 'updated-brand',
        'status' => BrandStatus::Inactive->value,
        'is_featured' => false,
        'meta_title' => 'Brand SEO',
        'meta_description' => 'Brand description for search.',
        'meta_keywords' => 'brand, gear',
        'sort_order' => 8,
        'logo' => UploadedFile::fake()->image('logo.png'),
    ])->assertRedirect(route('admin.brands.index'));

    $brand->refresh();

    expect($brand->name)->toBe('Updated Brand')
        ->and($brand->slug)->toBe('updated-brand')
        ->and($brand->status)->toBe(BrandStatus::Inactive)
        ->and($brand->meta_title)->toBe('Brand SEO')
        ->and($brand->logo_path)->not->toBeNull();
});

test('brand can be soft deleted and restored', function (): void {
    $brand = Brand::factory()->create();

    $this->delete(route('admin.brands.destroy', $brand))
        ->assertRedirect(route('admin.brands.index'));

    $this->assertSoftDeleted('brands', ['id' => $brand->id]);

    $this->post(route('admin.brands.restore', $brand->id))
        ->assertRedirect(route('admin.brands.index', ['trashed' => 1]));

    expect(Brand::query()->find($brand->id))->not->toBeNull();
});

test('dashboard shows brands stat and featured brands widget', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Brands')
        ->assertSee('Featured Brands');
});

test('brand show page renders', function (): void {
    $brand = Brand::query()->firstOrFail();

    $this->get(route('admin.brands.show', $brand))
        ->assertSuccessful()
        ->assertSee($brand->name)
        ->assertSee($brand->slug)
        ->assertSee('Edit brand');
});
