<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\HeroBanner;
use App\Models\HomepageSetting;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Services\MenuService;
use App\Services\Storefront\HomeService;
use App\Support\HomepageSettings;
use Database\Seeders\CommerceSeeder;
use Database\Seeders\HomepageSeeder;
use Database\Seeders\MarketingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
    $this->seed(CommerceSeeder::class);
    $this->seed(MarketingSeeder::class);
    $this->seed(HomepageSeeder::class);
});

test('homepage renders dynamic hero banners categories reviews and brands', function (): void {
    $banner = HeroBanner::query()->where('is_active', true)->firstOrFail();
    $review = Review::query()->approved()->firstOrFail();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee($banner->title)
        ->assertSee('Shop by category')
        ->assertSee('Best sellers')
        ->assertSee($review->author_name)
        ->assertSee('Join the ranks');
});

test('cached homepage hero banners hydrate correctly', function (): void {
    $banner = HeroBanner::query()->where('is_active', true)->firstOrFail();
    $homeService = app(HomeService::class);

    $homeService->heroBanners();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee($banner->title);
});

test('homepage hides sections disabled in homepage settings', function (): void {
    $settings = HomepageSettings::current();
    $sections = collect($settings->sections)
        ->map(fn (array $section): array => [
            'key' => $section['key'],
            'enabled' => ! in_array($section['key'], ['reviews', 'newsletter', 'brands'], true),
        ])
        ->all();

    HomepageSetting::query()->firstOrFail()->update(['sections' => $sections]);
    HomepageSettings::clearCache();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('Words from the ranks')
        ->assertDontSee('Join the ranks');
});

test('expired hero banners are not shown on the homepage', function (): void {
    HeroBanner::query()->update(['is_active' => false]);

    $expired = HeroBanner::factory()->expired()->create([
        'title' => 'Expired Hero Campaign',
        'is_active' => true,
    ]);

    HomepageSettings::clearCache();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee($expired->title);
});

test('best sellers are ranked from delivered orders', function (): void {
    $category = Category::query()->active()->whereNull('parent_id')->firstOrFail();

    $winner = Product::factory()->create([
        'name' => 'ZZZ Best Seller Winner Product',
        'category_id' => $category->id,
        'is_featured' => false,
        'stock_quantity' => 50,
    ]);

    $runnerUp = Product::factory()->create([
        'name' => 'AAA Best Seller Runner Product',
        'category_id' => $category->id,
        'is_featured' => false,
        'stock_quantity' => 50,
    ]);

    $order = Order::factory()->create(['status' => OrderStatus::Delivered]);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $winner->id, 'quantity' => 9999]);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $runnerUp->id, 'quantity' => 9998]);

    HomepageSetting::query()->firstOrFail()->update(['best_sellers_limit' => 8]);
    HomepageSettings::clearCache();

    $html = $this->get(route('home'))->assertSuccessful()->getContent();
    $section = strstr($html, 'id="best-sellers"');
    $section = $section ? substr($section, 0, strpos($section, '</section>') ?: strlen($section)) : $html;

    expect(strpos($section, $winner->name))->toBeLessThan(strpos($section, $runnerUp->name));
});

test('dynamic primary and footer menus render in the layout', function (): void {
    $menu = Menu::query()->where('slug', 'primary')->firstOrFail();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee($menu->allItems()->first()->label)
        ->assertSee('Shop')
        ->assertSee('Support');
});

test('cached navigation menus hydrate correctly', function (): void {
    $menu = Menu::query()->where('slug', 'primary')->firstOrFail();
    $parentItem = $menu->allItems()->whereNull('parent_id')->firstOrFail();
    $menuService = app(MenuService::class);

    $navigation = $menuService->navigation();

    expect($navigation['primary']->first())
        ->toBeInstanceOf(MenuItem::class)
        ->children->toBeInstanceOf(Collection::class);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee($parentItem->label);
});
