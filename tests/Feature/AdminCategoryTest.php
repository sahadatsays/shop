<?php

use App\Enums\CategoryStatus;
use App\Models\Category;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->seed(CommerceSeeder::class);
});

test('categories index page renders', function (): void {
    $this->get(route('admin.categories.index'))
        ->assertSuccessful()
        ->assertSee('Categories')
        ->assertSee('Apparel');
});

test('category can be created with slug generation', function (): void {
    $response = $this->post(route('admin.categories.store'), [
        'name' => 'Field Gear',
        'status' => CategoryStatus::Active->value,
        'sort_order' => 5,
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'name' => 'Field Gear',
        'slug' => 'field-gear',
        'status' => CategoryStatus::Active->value,
        'sort_order' => 5,
    ]);
});

test('category can be created as child of parent', function (): void {
    $parent = Category::query()->whereNull('parent_id')->first();

    $this->post(route('admin.categories.store'), [
        'name' => 'Subcategory Test',
        'parent_id' => $parent->id,
        'status' => CategoryStatus::Active->value,
        'sort_order' => 1,
    ])->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'name' => 'Subcategory Test',
        'parent_id' => $parent->id,
    ]);
});

test('category can be updated with image and seo fields', function (): void {
    $category = Category::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

    $this->put(route('admin.categories.update', $category), [
        'name' => 'Updated Name',
        'slug' => 'updated-name',
        'status' => CategoryStatus::Inactive->value,
        'meta_title' => 'SEO Title',
        'meta_description' => 'SEO description here.',
        'meta_keywords' => 'gear, outdoor',
        'sort_order' => 10,
        'image' => UploadedFile::fake()->image('category.jpg'),
    ])->assertRedirect(route('admin.categories.index'));

    $category->refresh();

    expect($category->name)->toBe('Updated Name')
        ->and($category->slug)->toBe('updated-name')
        ->and($category->status)->toBe(CategoryStatus::Inactive)
        ->and($category->meta_title)->toBe('SEO Title')
        ->and($category->image_path)->not->toBeNull();
});

test('category can be soft deleted and restored', function (): void {
    $category = Category::factory()->create();

    $this->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.categories.index'));

    $this->assertSoftDeleted('categories', ['id' => $category->id]);

    $this->post(route('admin.categories.restore', $category->id))
        ->assertRedirect(route('admin.categories.index', ['trashed' => 1]));

    expect(Category::query()->find($category->id))->not->toBeNull();
});

test('categories index shows product count', function (): void {
    $category = Category::query()->withCount('products')->first();

    $this->get(route('admin.categories.index'))
        ->assertSuccessful()
        ->assertSee((string) $category->products_count);
});

test('category show page renders', function (): void {
    $category = Category::query()->firstOrFail();

    $this->get(route('admin.categories.show', $category))
        ->assertSuccessful()
        ->assertSee($category->name)
        ->assertSee($category->slug)
        ->assertSee('Edit category');
});
