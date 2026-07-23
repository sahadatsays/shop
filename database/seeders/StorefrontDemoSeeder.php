<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
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
     *     description?: string,
     *     image: string,
     *     gallery?: list<string>,
     *     specifications?: list<array{name: string, value: string}>,
     *     attributes?: list<array{name: string, value: string}>,
     *     related?: list<string>,
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
                'description' => "The Ranger Field Jacket is our flagship outerwear piece — a modern reinterpretation of the classic M-65, built from 10 oz waxed organic canvas and lined with brushed cotton twill. Designed by a team of Army and Marine Corps veterans, every seam is placed with purpose: articulated elbows for range of motion, a storm flap that actually stops wind, and pockets positioned where your hands expect them.\n\nGarment-dyed and stone-washed for a broken-in feel from day one, it only gets better with wear. Five percent of every purchase funds veteran career transition programs.",
                'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop',
                'gallery' => [
                    'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1539533018447-63fcce267808?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800&q=70&auto=format&fit=crop',
                ],
                'specifications' => [
                    ['name' => 'Weight', 'value' => '1.4 kg / 3.1 lb'],
                    ['name' => 'Shell', 'value' => '10 oz waxed organic canvas'],
                    ['name' => 'Lining', 'value' => 'Brushed cotton twill'],
                    ['name' => 'Hardware', 'value' => 'Antique bronze YKK zippers'],
                    ['name' => 'Pockets', 'value' => '6 external, 2 internal'],
                    ['name' => 'Fit', 'value' => 'Regular — true to size'],
                    ['name' => 'Origin', 'value' => 'Cut & sewn in the USA'],
                    ['name' => 'Warranty', 'value' => 'Lifetime craftsmanship'],
                ],
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Olive Drab'],
                    ['name' => 'Color', 'value' => 'Coyote Brown'],
                    ['name' => 'Color', 'value' => 'Midnight Navy'],
                    ['name' => 'Color', 'value' => 'Stone Gray'],
                    ['name' => 'Size', 'value' => 'S'],
                    ['name' => 'Size', 'value' => 'M'],
                    ['name' => 'Size', 'value' => 'L'],
                    ['name' => 'Size', 'value' => 'XL'],
                    ['name' => 'Size', 'value' => 'XXL'],
                    ['name' => 'Material', 'value' => '100% organic cotton canvas shell, paraffin-wax impregnated for weather resistance'],
                    ['name' => 'Material', 'value' => '100% cotton twill lining, brushed for warmth and comfort'],
                    ['name' => 'Material', 'value' => 'Solid antique bronze hardware — no plated alloys'],
                    ['name' => 'Material', 'value' => 'Corozo nut buttons, hand-finished'],
                    ['name' => 'Care', 'value' => 'Do not machine wash — spot clean with cold water and a soft brush'],
                    ['name' => 'Care', 'value' => 'Air dry away from direct heat; never tumble dry'],
                    ['name' => 'Care', 'value' => 'Re-wax annually with the included wax bar for continued weather resistance'],
                ],
                'related' => ['patriot-canvas-rucksack', 'honor-edc-kit', 'sentinel-field-watch', 'valor-wool-beanie'],
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
                'gallery' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1622560480605-83d5f164b51d?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1581605405669-61f3c2b3a5b3?w=800&q=70&auto=format&fit=crop',
                ],
                'specifications' => [
                    ['name' => 'Capacity', 'value' => '32 L'],
                    ['name' => 'Shell', 'value' => '18 oz waxed canvas'],
                    ['name' => 'Straps', 'value' => 'Vegetable-tanned leather'],
                    ['name' => 'Weight', 'value' => '1.8 kg / 4.0 lb'],
                ],
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Coyote Brown'],
                    ['name' => 'Color', 'value' => 'Olive Drab'],
                    ['name' => 'Material', 'value' => 'Waxed canvas body with leather base and strap reinforcements'],
                    ['name' => 'Care', 'value' => 'Brush clean and air dry; condition leather straps seasonally'],
                ],
                'related' => ['ranger-field-jacket', 'trail-proven-boots', 'service-insulated-bottle'],
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
                'specifications' => [
                    ['name' => 'Movement', 'value' => 'Automatic, 24-jewel'],
                    ['name' => 'Crystal', 'value' => 'Sapphire with anti-reflective coating'],
                    ['name' => 'Water resistance', 'value' => '100 m'],
                    ['name' => 'Case diameter', 'value' => '40 mm'],
                ],
                'related' => ['heritage-automatic-watch', 'everyday-leather-wallet'],
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
                'description' => "Garment-dyed organic cotton tee with a relaxed fit and soft hand feel. Pre-washed for comfort from the first wear and built to hold its shape through repeated laundering.\n\nA portion of every sale supports local veteran outreach programs.",
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop',
                'gallery' => [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&q=70&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&q=70&auto=format&fit=crop',
                ],
                'specifications' => [
                    ['name' => 'Fabric', 'value' => '100% organic cotton jersey'],
                    ['name' => 'Weight', 'value' => '180 gsm'],
                    ['name' => 'Fit', 'value' => 'Relaxed'],
                ],
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Olive Drab'],
                    ['name' => 'Color', 'value' => 'Midnight Navy'],
                    ['name' => 'Color', 'value' => 'Stone Gray'],
                    ['name' => 'Size', 'value' => 'S'],
                    ['name' => 'Size', 'value' => 'M'],
                    ['name' => 'Size', 'value' => 'L'],
                    ['name' => 'Size', 'value' => 'XL'],
                    ['name' => 'Material', 'value' => 'Garment-dyed organic cotton with soft-hand finish'],
                    ['name' => 'Care', 'value' => 'Machine wash cold, tumble dry low'],
                ],
                'related' => ['ranger-field-jacket', 'valor-wool-beanie'],
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
                'related' => ['everyday-leather-wallet', 'service-insulated-bottle'],
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
                'attributes' => [
                    ['name' => 'Size', 'value' => '8'],
                    ['name' => 'Size', 'value' => '9'],
                    ['name' => 'Size', 'value' => '10'],
                    ['name' => 'Size', 'value' => '11'],
                    ['name' => 'Size', 'value' => '12'],
                    ['name' => 'Color', 'value' => 'Coyote Brown'],
                ],
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
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Olive Drab'],
                    ['name' => 'Color', 'value' => 'Midnight Navy'],
                ],
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
        /** @var list<array{product: Product, related: list<string>}> $relatedQueue */
        $relatedQueue = [];

        foreach ($this->catalog() as $item) {
            $category = Category::query()->where('name', $item['category'])->first();
            $brand = Brand::query()->where('name', $item['brand'])->first();

            if (! $category || ! $brand) {
                continue;
            }

            $product = Product::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $item['name'],
                    'sku' => strtoupper(str_replace('-', '', $item['slug'])).'-DEMO',
                    'short_description' => $item['short_description'],
                    'description' => $item['description'] ?? ($item['short_description'].' Crafted with the honor, discipline, and quality of those who served.'),
                    'price_cents' => $item['price_cents'],
                    'compare_at_price_cents' => $item['compare_at_price_cents'] ?? null,
                    'stock_quantity' => $item['stock_quantity'],
                    'low_stock_threshold' => 10,
                    'status' => ProductStatus::Published,
                    'is_featured' => $item['is_featured'] ?? false,
                    'is_new_arrival' => $item['is_new_arrival'] ?? false,
                    'sort_order' => 0,
                ],
            );

            $this->syncGallery($product, $item);
            $this->syncSpecifications($product, $item['specifications'] ?? []);
            $this->syncAttributes($product, $item['attributes'] ?? []);

            $relatedQueue[] = [
                'product' => $product,
                'related' => $item['related'] ?? [],
            ];
        }

        foreach ($relatedQueue as $entry) {
            $this->syncRelatedProducts($entry['product'], $entry['related']);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncGallery(Product $product, array $item): void
    {
        /** @var list<string> $paths */
        $paths = $item['gallery'] ?? [$item['image']];

        $product->images()->delete();

        foreach ($paths as $index => $path) {
            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $path,
                'alt_text' => $index === 0 ? $product->name : $product->name.' — view '.($index + 1),
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }

    /**
     * @param  list<array{name: string, value: string}>  $specifications
     */
    private function syncSpecifications(Product $product, array $specifications): void
    {
        $product->specifications()->delete();

        foreach ($specifications as $index => $specification) {
            ProductSpecification::query()->create([
                'product_id' => $product->id,
                'name' => $specification['name'],
                'value' => $specification['value'],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * @param  list<array{name: string, value: string}>  $attributes
     */
    private function syncAttributes(Product $product, array $attributes): void
    {
        $product->attributes()->delete();

        foreach ($attributes as $index => $attribute) {
            ProductAttribute::query()->create([
                'product_id' => $product->id,
                'name' => $attribute['name'],
                'value' => $attribute['value'],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * @param  list<string>  $relatedSlugs
     */
    private function syncRelatedProducts(Product $product, array $relatedSlugs): void
    {
        if ($relatedSlugs === []) {
            return;
        }

        $relatedIds = Product::query()
            ->whereIn('slug', $relatedSlugs)
            ->pluck('id', 'slug');

        $syncData = [];

        foreach ($relatedSlugs as $index => $slug) {
            if (! isset($relatedIds[$slug])) {
                continue;
            }

            $syncData[$relatedIds[$slug]] = ['sort_order' => $index];
        }

        $product->relatedProducts()->sync($syncData);
    }
}
