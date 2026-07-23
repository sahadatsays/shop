<?php

namespace Database\Seeders;

use App\Enums\PromoBannerLayout;
use App\Models\Category;
use App\Models\HeroBanner;
use App\Models\HomepageFeature;
use App\Models\HomepageSetting;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Product;
use App\Models\PromoBanner;
use App\Models\Review;
use App\Support\HomepageSettings;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSettings::clearCache();

        HomepageSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'featured_products_limit' => 8,
                'new_arrivals_limit' => 8,
                'best_sellers_limit' => 4,
                'brands_limit' => 8,
                'categories_limit' => 8,
                'reviews_limit' => 6,
                'new_badge_days' => 30,
                'hide_out_of_stock' => true,
                'sections' => HomepageSetting::defaultSections(),
                'popular_searches' => ['jackets', 'flags', 'challenge coins', 'boots', 'packs'],
                'meta_title' => 'Valor Supply Co. — Veteran-Owned Premium Gear',
                'meta_description' => 'Shop premium apparel, outdoor gear, and collectibles from a veteran-owned store built on honor, quality, and service.',
                'meta_keywords' => 'veteran gear, outdoor apparel, challenge coins, flags',
            ],
        );

        Category::query()
            ->whereNull('parent_id')
            ->ordered()
            ->limit(8)
            ->update(['is_featured' => true]);

        HeroBanner::query()->updateOrCreate(
            ['title' => 'Honor in every stitch and seam.'],
            [
                'subtitle' => 'Veteran owned & operated since 2019',
                'description' => 'Premium apparel, collectibles, and field gear designed by veterans who hold their products to the same standard they held their service.',
                'badge_text' => 'Veteran owned & operated since 2019',
                'desktop_image_path' => 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=2000&q=75&auto=format&fit=crop',
                'mobile_image_path' => 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=800&q=70&auto=format&fit=crop',
                'primary_label' => 'Shop best sellers',
                'primary_url' => '#best-sellers',
                'secondary_label' => 'Our story',
                'secondary_url' => '/about',
                'sort_order' => 0,
                'is_active' => true,
            ],
        );

        HeroBanner::query()->updateOrCreate(
            ['title' => 'Field-ready gear for every mission.'],
            [
                'subtitle' => 'Spring expedition drop',
                'description' => 'Packs, layers, and tools built for hard miles and long seasons.',
                'badge_text' => 'New season',
                'desktop_image_path' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=2000&q=75&auto=format&fit=crop',
                'mobile_image_path' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&q=70&auto=format&fit=crop',
                'primary_label' => 'Shop the collection',
                'primary_url' => '/shop',
                'secondary_label' => 'View all products',
                'secondary_url' => '/shop',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        PromoBanner::query()->updateOrCreate(
            ['title' => 'Expedition essentials'],
            [
                'layout' => PromoBannerLayout::Double,
                'image_path' => 'https://images.unsplash.com/photo-1501554728187-ce583db33af7?w=1200&q=75&auto=format&fit=crop',
                'button_label' => 'Shop collection',
                'url' => '/shop',
                'sort_order' => 0,
                'is_active' => true,
            ],
        );

        PromoBanner::query()->updateOrCreate(
            ['title' => 'Heritage apparel'],
            [
                'layout' => PromoBannerLayout::Double,
                'image_path' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=1200&q=75&auto=format&fit=crop',
                'button_label' => 'Explore apparel',
                'url' => '/shop',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $features = [
            ['icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm-1.5 13.5-3-3 1.4-1.4 1.6 1.6 4.6-4.6 1.4 1.4-6 6Z', 'title' => 'Authentic Products', 'description' => 'Licensed, verified, and sourced with integrity'],
            ['icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z', 'title' => 'Veteran Owned', 'description' => 'Founded and run by those who served'],
            ['icon' => 'M3 7h11v9H3zM14 10h4l3 3v3h-7zM7 19a1.6 1.6 0 1 0 0-3.2A1.6 1.6 0 0 0 7 19Zm10.5 0a1.6 1.6 0 1 0 0-3.2 1.6 1.6 0 0 0 0 3.2Z', 'title' => 'Fast Shipping', 'description' => 'Free express delivery on orders over $75'],
            ['icon' => 'M4 10h16v10H4zM8 10V7a4 4 0 0 1 8 0v3', 'title' => 'Secure Payment', 'description' => '256-bit encrypted checkout, every order'],
            ['icon' => 'M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z', 'title' => 'Lifetime Support', 'description' => 'Craftsmanship warranty that outlasts trends'],
        ];

        foreach ($features as $index => $feature) {
            HomepageFeature::query()->updateOrCreate(
                ['title' => $feature['title']],
                [
                    'icon' => $feature['icon'],
                    'description' => $feature['description'],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
        }

        $reviewSeed = [
            ['author_name' => 'Marcus T.', 'title' => 'Army veteran, 2011–2019', 'rating' => 5, 'body' => 'The Ranger jacket is the best piece of kit I have owned since my service days. Quality you can feel in every seam.'],
            ['author_name' => 'Sarah K.', 'title' => 'Military spouse', 'rating' => 5, 'body' => 'Bought my husband the rucksack for his retirement. He inspects everything — this passed on the first look.'],
            ['author_name' => 'David R.', 'title' => 'Marine Corps veteran', 'rating' => 5, 'body' => 'Fast shipping, honest sizing, and the profits give back to the community. This is how a store should be run.'],
            ['author_name' => 'Elena M.', 'title' => 'Air Force, active duty', 'rating' => 4, 'body' => 'The challenge coins are stunning — heavy, detailed, and beautifully finished. My squadron ordered a full set.'],
            ['author_name' => 'James W.', 'title' => 'Navy veteran, 1998–2010', 'rating' => 5, 'body' => 'Lifetime warranty is not marketing talk here. They repaired my wallet stitching free of charge, three years in.'],
            ['author_name' => 'Priya N.', 'title' => 'Verified buyer', 'rating' => 5, 'body' => 'Excellent quality and packaging. Will definitely order again for gifts.'],
        ];

        $productIds = Product::query()->published()->limit(6)->pluck('id');

        foreach ($reviewSeed as $index => $review) {
            Review::query()->updateOrCreate(
                ['author_name' => $review['author_name'], 'body' => $review['body']],
                [
                    'product_id' => $productIds[$index] ?? $productIds->first(),
                    'rating' => $review['rating'],
                    'title' => $review['title'],
                    'is_approved' => true,
                ],
            );
        }

        $this->seedMenus();
    }

    private function seedMenus(): void
    {
        $primary = Menu::query()->updateOrCreate(
            ['slug' => 'primary'],
            ['name' => 'Primary Header', 'location' => 'header', 'is_active' => true],
        );

        $primary->allItems()->delete();

        $shop = MenuItem::query()->create([
            'menu_id' => $primary->id,
            'label' => 'Shop',
            'route_name' => 'shop',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $categoryLinks = [
            ['Apparel', 'Jackets, tees & headwear'],
            ['Military Collectibles', 'Medals, patches & memorabilia'],
            ['Outdoor Gear', 'Packs, tools & field equipment'],
            ['Flags', 'Stitched & embroidered colors'],
            ['Challenge Coins', 'Unit & commemorative coins'],
            ['Books', 'History, memoirs & field guides'],
            ['Accessories', 'Wallets, watches & EDC'],
            ['Home Decor', 'Prints, signs & barware'],
        ];

        foreach ($categoryLinks as $index => [$label]) {
            $category = Category::query()->where('name', $label)->first();

            MenuItem::query()->create([
                'menu_id' => $primary->id,
                'parent_id' => $shop->id,
                'label' => $label,
                'url' => $category ? route('shop', ['categories' => [$category->slug]], false) : '/shop',
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        foreach ([
            ['Categories', 'categories', 1],
            ['Our Story', 'about', 2],
            ['Support', 'support', 3],
        ] as [$label, $route, $order]) {
            MenuItem::query()->create([
                'menu_id' => $primary->id,
                'label' => $label,
                'route_name' => $route,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }

        $footerMenus = [
            'footer-shop' => [
                'name' => 'Shop',
                'items' => [
                    ['All Products', 'shop'],
                    ['Apparel', null, '/shop'],
                    ['Outdoor Gear', null, '/shop'],
                    ['Challenge Coins', null, '/shop'],
                    ['Flags', null, '/shop'],
                    ['Gift Cards', null, '#'],
                ],
            ],
            'footer-company' => [
                'name' => 'Company',
                'items' => [
                    ['Our Story', 'about'],
                    ['Veteran Owned', 'about'],
                    ['Giving Back', 'about'],
                    ['Wholesale', null, '#'],
                    ['Careers', null, '#'],
                    ['Press', null, '#'],
                ],
            ],
            'footer-support' => [
                'name' => 'Support',
                'items' => [
                    ['Contact Us', 'contact'],
                    ['Shipping & Returns', 'support'],
                    ['Order Tracking', 'track'],
                    ['FAQ', 'support'],
                    ['Warranty', 'support'],
                    ['Size Guides', 'support'],
                ],
            ],
            'footer-legal' => [
                'name' => 'Legal',
                'items' => [
                    ['Privacy Policy', null, '#'],
                    ['Terms of Service', null, '#'],
                    ['Refund Policy', null, '#'],
                    ['Accessibility', null, '#'],
                ],
            ],
        ];

        foreach ($footerMenus as $slug => $menuData) {
            $menu = Menu::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $menuData['name'], 'location' => 'footer', 'is_active' => true],
            );

            $menu->allItems()->delete();

            foreach ($menuData['items'] as $index => $item) {
                MenuItem::query()->create([
                    'menu_id' => $menu->id,
                    'label' => $item[0],
                    'route_name' => $item[1] ?? null,
                    'url' => $item[2] ?? null,
                    'sort_order' => $index,
                    'is_active' => true,
                ]);
            }
        }
    }
}
