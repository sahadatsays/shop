<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class StorefrontDemoSeeder extends Seeder
{
    /**
     * @return list<array{
     *     name: string,
     *     slug: string,
     *     category: string,
     *     brand: string,
     *     price_cents: int,
     *     compare_at_price_cents?: int|null,
     *     stock_quantity: int,
     *     is_featured?: bool,
     *     is_new_arrival?: bool,
     *     short_description: string,
     *     image: string,
     * }>
     */
    private function catalog(): array
    {
        return [
            [
                'name' => 'Ranger Field Jacket',
                'slug' => 'ranger-field-jacket',
                'category' => 'Apparel',
                'brand' => 'Valor Supply Co.',
                'price_cents' => 18900,
                'compare_at_price_cents' => 24900,
                'stock_quantity' => 14,
                'is_featured' => true,
                'short_description' => 'Waxed canvas field jacket built for three-season wear with reinforced elbows and bronze hardware.',
                'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Patriot Canvas Rucksack',
                'slug' => 'patriot-canvas-rucksack',
                'category' => 'Outdoor Gear',
                'brand' => 'Garrison Works',
                'price_cents' => 14900,
                'stock_quantity' => 32,
                'is_featured' => true,
                'is_new_arrival' => true,
                'short_description' => 'Heavy-duty canvas pack with leather reinforcements and a lifetime craftsmanship warranty.',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Sentinel Field Watch',
                'slug' => 'sentinel-field-watch',
                'category' => 'Accessories',
                'brand' => 'Sentinel & Sons',
                'price_cents' => 22900,
                'compare_at_price_cents' => 27900,
                'stock_quantity' => 18,
                'is_featured' => true,
                'short_description' => 'Automatic field watch with sapphire crystal and a matte olive dial inspired by military issue.',
                'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Garrison Heritage Tee',
                'slug' => 'garrison-heritage-tee',
                'category' => 'Apparel',
                'brand' => 'Old Glory Textiles',
                'price_cents' => 3800,
                'stock_quantity' => 85,
                'is_new_arrival' => true,
                'short_description' => 'Garment-dyed organic cotton tee with a relaxed fit and soft hand feel.',
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Honor EDC Kit',
                'slug' => 'honor-edc-kit',
                'category' => 'Everyday Carry',
                'brand' => 'Valor Supply Co.',
                'price_cents' => 9600,
                'compare_at_price_cents' => 12000,
                'stock_quantity' => 9,
                'short_description' => 'Compact everyday carry kit with a leather wallet, key organizer, and field notebook.',
                'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Anniversary Stitched Flag',
                'slug' => 'anniversary-stitched-flag',
                'category' => 'Flags',
                'brand' => 'Old Glory Textiles',
                'price_cents' => 12000,
                'stock_quantity' => 6,
                'short_description' => 'Hand-stitched commemorative flag made in Texas with embroidered stars and reinforced header.',
                'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Heritage Automatic Watch',
                'slug' => 'heritage-automatic-watch',
                'category' => 'Accessories',
                'brand' => 'Sentinel & Sons',
                'price_cents' => 44900,
                'stock_quantity' => 11,
                'is_featured' => true,
                'short_description' => 'Limited-run automatic with a bronze case and Horween leather strap.',
                'image' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Field Manual Collection',
                'slug' => 'field-manual-collection',
                'category' => 'Books',
                'brand' => 'Basecamp Provisions',
                'price_cents' => 5400,
                'compare_at_price_cents' => 6800,
                'stock_quantity' => 40,
                'short_description' => 'Curated set of field manuals covering navigation, campcraft, and leadership under pressure.',
                'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Trail Proven Boots',
                'slug' => 'trail-proven-boots',
                'category' => 'Outdoor Gear',
                'brand' => 'Garrison Works',
                'price_cents' => 21000,
                'stock_quantity' => 22,
                'short_description' => 'Full-grain leather hiking boots with a storm welt and Vibram outsole.',
                'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Valor Wool Beanie',
                'slug' => 'valor-wool-beanie',
                'category' => 'Apparel',
                'brand' => 'Valor Supply Co.',
                'price_cents' => 3400,
                'stock_quantity' => 56,
                'short_description' => 'Merino wool beanie in olive drab — warm, breathable, and packable.',
                'image' => 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Service Insulated Bottle',
                'slug' => 'service-insulated-bottle',
                'category' => 'Everyday Carry',
                'brand' => 'Basecamp Provisions',
                'price_cents' => 4200,
                'stock_quantity' => 64,
                'is_new_arrival' => true,
                'short_description' => 'Double-wall stainless bottle keeps drinks hot for 12 hours or cold for 24.',
                'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&q=70&auto=format&fit=crop',
            ],
            [
                'name' => 'Everyday Leather Wallet',
                'slug' => 'everyday-leather-wallet',
                'category' => 'Everyday Carry',
                'brand' => 'Garrison Works',
                'price_cents' => 7900,
                'stock_quantity' => 38,
                'short_description' => 'Minimalist full-grain leather bifold with RFID blocking and a lifetime warranty.',
                'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&q=70&auto=format&fit=crop',
            ],
        ];
    }

    public function run(): void
    {
        foreach ($this->catalog() as $item) {
            if (Product::query()->where('slug', $item['slug'])->exists()) {
                continue;
            }

            $category = Category::query()->where('name', $item['category'])->first();
            $brand = Brand::query()->where('name', $item['brand'])->first();

            if (! $category || ! $brand) {
                continue;
            }

            $product = Product::query()->create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $item['name'],
                'slug' => $item['slug'],
                'sku' => strtoupper(str_replace('-', '', $item['slug'])).'-DEMO',
                'short_description' => $item['short_description'],
                'description' => $item['short_description'].' Crafted with the honor, discipline, and quality of those who served.',
                'price_cents' => $item['price_cents'],
                'compare_at_price_cents' => $item['compare_at_price_cents'] ?? null,
                'stock_quantity' => $item['stock_quantity'],
                'low_stock_threshold' => 10,
                'status' => ProductStatus::Published,
                'is_featured' => $item['is_featured'] ?? false,
                'is_new_arrival' => $item['is_new_arrival'] ?? false,
                'sort_order' => 0,
            ]);

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $item['image'],
                'alt_text' => $item['name'],
                'sort_order' => 0,
                'is_primary' => true,
            ]);
        }
    }
}
