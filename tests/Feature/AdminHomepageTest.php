<?php

use App\Models\HeroBanner;
use App\Models\HomepageSetting;
use App\Models\Menu;
use App\Models\NewsletterSubscriber;
use App\Models\Review;
use App\Support\HomepageSettings;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\HomepageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    Cache::flush();
    $this->seed(AdminAccessSeeder::class);
    $this->seed(HomepageSeeder::class);
    actingAsAdmin();
});

test('homepage settings page renders for authorized admin', function (): void {
    $this->get(route('admin.homepage.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Homepage Settings')
        ->assertSee('Section visibility');
});

test('homepage settings can be updated and clears cache', function (): void {
    $this->put(route('admin.homepage.settings.update'), [
        'featured_products_limit' => 6,
        'new_arrivals_limit' => 6,
        'best_sellers_limit' => 3,
        'brands_limit' => 6,
        'categories_limit' => 6,
        'reviews_limit' => 4,
        'new_badge_days' => 14,
        'hide_out_of_stock' => true,
        'enabled_sections' => ['hero', 'featured_products', 'newsletter'],
        'popular_searches' => 'boots, jackets',
        'meta_title' => 'Updated Homepage Title',
        'meta_description' => 'Updated description',
        'meta_keywords' => 'gear, veteran',
    ])->assertRedirect(route('admin.homepage.settings.edit'));

    $settings = HomepageSetting::query()->firstOrFail();

    expect($settings->featured_products_limit)->toBe(6)
        ->and($settings->meta_title)->toBe('Updated Homepage Title')
        ->and($settings->enabledSectionKeys())->toBe(['hero', 'featured_products', 'newsletter']);

    HomepageSettings::current();

    $cached = Cache::get('homepage.settings');
    expect($cached)->toBeArray()
        ->and($cached['meta_title'])->toBe('Updated Homepage Title');
});

test('hero banner can be created updated and deleted', function (): void {
    $this->get(route('admin.homepage.hero-banners.index'))
        ->assertSuccessful()
        ->assertSee('Hero Banners');

    $this->post(route('admin.homepage.hero-banners.store'), [
        'title' => 'Admin Test Hero',
        'subtitle' => 'Test subtitle',
        'description' => 'Test description',
        'primary_label' => 'Shop now',
        'primary_url' => '/shop',
        'sort_order' => 5,
        'is_active' => true,
        'desktop_image' => UploadedFile::fake()->image('hero.jpg'),
    ])->assertRedirect(route('admin.homepage.hero-banners.index'));

    $banner = HeroBanner::query()->where('title', 'Admin Test Hero')->firstOrFail();

    $this->put(route('admin.homepage.hero-banners.update', $banner), [
        'title' => 'Updated Admin Hero',
        'sort_order' => 2,
        'is_active' => true,
    ])->assertRedirect(route('admin.homepage.hero-banners.index'));

    expect($banner->fresh()->title)->toBe('Updated Admin Hero');

    $this->delete(route('admin.homepage.hero-banners.destroy', $banner))
        ->assertRedirect(route('admin.homepage.hero-banners.index'));

    $this->assertDatabaseMissing('hero_banners', ['id' => $banner->id]);
});

test('homepage feature can be managed from admin', function (): void {
    $this->post(route('admin.homepage.features.store'), [
        'icon' => 'M12 2 4 5v6',
        'title' => 'Admin Feature',
        'description' => 'Managed from admin panel',
        'sort_order' => 1,
        'is_active' => true,
    ])->assertRedirect(route('admin.homepage.features.index'));

    $this->assertDatabaseHas('homepage_features', [
        'title' => 'Admin Feature',
        'is_active' => true,
    ]);
});

test('menu items can be added from admin', function (): void {
    $menu = Menu::query()->where('slug', 'primary')->firstOrFail();

    $this->get(route('admin.homepage.menus.edit', $menu))
        ->assertSuccessful()
        ->assertSee($menu->name);

    $this->post(route('admin.homepage.menus.items.store', $menu), [
        'label' => 'Admin Added Link',
        'route_name' => 'shop',
        'sort_order' => 99,
        'is_active' => true,
    ])->assertRedirect(route('admin.homepage.menus.edit', $menu));

    $this->assertDatabaseHas('menu_items', [
        'menu_id' => $menu->id,
        'label' => 'Admin Added Link',
        'route_name' => 'shop',
    ]);
});

test('newsletter subscribers index renders', function (): void {
    NewsletterSubscriber::factory()->create(['email' => 'phase2-test@example.com']);

    $this->get(route('admin.homepage.newsletter-subscribers.index'))
        ->assertSuccessful()
        ->assertSee('phase2-test@example.com');
});

test('homepage review can be created and approved', function (): void {
    $this->post(route('admin.homepage.reviews.store'), [
        'author_name' => 'Sgt. Admin',
        'rating' => 5,
        'title' => 'Great gear',
        'body' => 'Managed from the admin panel.',
        'is_approved' => true,
    ])->assertRedirect(route('admin.homepage.reviews.index'));

    $review = Review::query()->where('author_name', 'Sgt. Admin')->firstOrFail();

    expect($review->is_approved)->toBeTrue();

    $this->get(route('admin.homepage.reviews.index'))
        ->assertSuccessful()
        ->assertSee('Sgt. Admin');
});
