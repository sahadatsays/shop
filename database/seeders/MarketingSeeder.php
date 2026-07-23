<?php

namespace Database\Seeders;

use App\Enums\DiscountType;
use App\Enums\PromotionPlacement;
use App\Enums\PromotionType;
use App\Models\Collection;
use App\Models\Discount;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        $discount = Discount::query()->updateOrCreate(
            ['code' => 'VALOR10'],
            [
                'name' => 'Valor 10% Off',
                'description' => 'Ten percent off qualifying orders.',
                'type' => DiscountType::Percent,
                'value' => 10,
                'min_order_cents' => 5000,
                'max_uses' => 1000,
                'is_active' => true,
                'starts_at' => now()->subWeek(),
                'ends_at' => now()->addMonths(3),
            ],
        );

        $offer = Offer::query()->updateOrCreate(
            ['slug' => 'spring-field-gear'],
            [
                'name' => 'Spring Field Gear Event',
                'headline' => 'Spring field gear event',
                'subheadline' => 'Save on jackets, packs, and trail essentials.',
                'body' => 'Limited-time pricing on selected field gear.',
                'cta_label' => 'Shop the event',
                'cta_url' => '/shop?on_sale=1',
                'discount_id' => $discount->id,
                'is_active' => true,
                'starts_at' => now()->subDays(3),
                'ends_at' => now()->addWeeks(2),
                'sort_order' => 1,
            ],
        );

        $saleProducts = Product::query()->published()->onSale()->limit(6)->get();

        if ($saleProducts->isEmpty()) {
            $saleProducts = Product::query()->published()->limit(6)->get()->each(function (Product $product): void {
                $product->update([
                    'compare_at_price_cents' => $product->price_cents + 2000,
                ]);
            });
        }

        $offer->products()->sync(
            $saleProducts->values()->mapWithKeys(fn (Product $product, int $index): array => [
                $product->id => ['sale_price_cents' => $product->price_cents, 'sort_order' => $index],
            ])->all(),
        );

        $featuredProducts = Product::query()->published()->limit(12)->get();

        $collections = [
            [
                'slug' => 'expedition-collection',
                'name' => 'The Expedition Collection',
                'description' => 'Packs, layers, and tools for the backcountry.',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'heritage-apparel',
                'name' => 'Heritage Apparel',
                'description' => 'Garment-dyed classics, built to break in.',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'trail-ready',
                'name' => 'Trail Ready',
                'description' => 'Boots and gear proven on hard miles.',
                'is_featured' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($collections as $index => $data) {
            $collection = Collection::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_featured' => $data['is_featured'],
                    'is_active' => true,
                    'sort_order' => $data['sort_order'],
                ],
            );

            $collection->products()->sync(
                $featuredProducts->slice($index * 4, 4)->values()->mapWithKeys(
                    fn (Product $product, int $sort): array => [$product->id => ['sort_order' => $sort]],
                )->all(),
            );
        }

        Promotion::query()->updateOrCreate(
            ['slug' => 'home-hero-spring-event'],
            [
                'name' => 'Home Hero Spring Event',
                'type' => PromotionType::Banner,
                'placement' => PromotionPlacement::HomeHero,
                'headline' => 'Honor in every stitch and seam.',
                'subheadline' => 'Spring field gear event — limited-time savings on expedition essentials.',
                'cta_label' => 'Shop best sellers',
                'cta_url' => '#best-sellers',
                'offer_id' => $offer->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        Promotion::query()->updateOrCreate(
            ['slug' => 'weekend-flash-sale'],
            [
                'name' => 'Weekend Flash Sale',
                'type' => PromotionType::Countdown,
                'placement' => PromotionPlacement::ShopTop,
                'headline' => 'Weekend flash sale ends soon',
                'subheadline' => 'Extra savings on select sale items while supplies last.',
                'cta_label' => 'Shop sale items',
                'cta_url' => '/shop?on_sale=1',
                'offer_id' => $offer->id,
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(3),
                'sort_order' => 1,
            ],
        );
    }
}
