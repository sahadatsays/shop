<?php

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerNote;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Warehouse;
use App\Services\Admin\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CommerceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Apparel',
            'Outdoor Gear',
            'Accessories',
            'Flags',
            'Books',
            'Everyday Carry',
        ])->map(fn (string $name) => Category::factory()->create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'sort_order' => fake()->numberBetween(1, 10),
        ]));

        $apparel = $categories->firstWhere('name', 'Apparel');
        if ($apparel) {
            Category::factory()->count(2)->child($apparel)->create();
        }

        $brands = collect([
            'Valor Supply Co.',
            'Garrison Works',
            'Sentinel & Sons',
            'Basecamp Provisions',
            'Old Glory Textiles',
        ])->map(fn (string $name, int $index) => Brand::factory()->create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'is_featured' => $index < 3,
            'sort_order' => $index + 1,
            'description' => fake()->paragraph(),
        ]));

        $products = $categories->flatMap(function (Category $category) use ($brands): Collection {
            return Product::factory()
                ->count(6)
                ->sequence(
                    ['stock_quantity' => 45, 'is_featured' => true],
                    ['stock_quantity' => 28],
                    ['stock_quantity' => 6, 'low_stock_threshold' => 10],
                    ['stock_quantity' => 22, 'is_new_arrival' => true],
                    ['stock_quantity' => 15],
                    ['stock_quantity' => 0],
                )
                ->create([
                    'category_id' => $category->id,
                    'brand_id' => $brands->random()->id,
                ]);
        });

        $products->each(function (Product $product, int $index): void {
            if (fake()->boolean(35)) {
                $product->update([
                    'compare_at_price_cents' => $product->price_cents + fake()->numberBetween(800, 6000),
                ]);
            }

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => 'https://images.unsplash.com/photo-'.fake()->randomElement([
                    '1551028719-00167b16eac5',
                    '1553062407-98eeb64c6a62',
                    '1521572163474-6864f9cf17ab',
                    '1524592094714-0f0654e20314',
                    '1512436991641-6745cdb1723f',
                    '1512820790803-83ca734da794',
                ]).'?w=800&q=70&auto=format&fit=crop',
                'alt_text' => $product->name,
                'sort_order' => 0,
                'is_primary' => true,
            ]);
        });

        $warehouse = Warehouse::query()->create([
            'name' => 'Fort Worth Distribution Center',
            'code' => 'FTW-01',
            'city' => 'Fort Worth',
            'state' => 'TX',
            'country' => 'US',
            'address' => '1200 Logistics Parkway',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $inventory = app(InventoryService::class);

        $products->each(function (Product $product) use ($inventory, $warehouse): void {
            if ($product->stock_quantity > 0) {
                $inventory->initializeStock($product, $product->stock_quantity, $warehouse);
            }
        });

        $customers = Customer::factory()->count(24)->create();

        $customers->take(8)->each(function (Customer $customer): void {
            CustomerAddress::query()->create([
                'customer_id' => $customer->id,
                'label' => 'Home',
                'type' => AddressType::Shipping,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'line1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->stateAbbr(),
                'postal_code' => fake()->postcode(),
                'country' => 'US',
                'is_default' => true,
            ]);

            if (fake()->boolean(40)) {
                CustomerAddress::query()->create([
                    'customer_id' => $customer->id,
                    'label' => 'Billing',
                    'type' => AddressType::Billing,
                    'name' => $customer->name,
                    'line1' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->stateAbbr(),
                    'postal_code' => fake()->postcode(),
                    'country' => 'US',
                    'is_default' => false,
                ]);
            }

            CustomerNote::query()->create([
                'customer_id' => $customer->id,
                'body' => fake()->sentence(),
                'author_name' => 'Admin',
            ]);
        });

        foreach (range(1, 12) as $monthsAgo) {
            $month = now()->subMonths($monthsAgo - 1)->startOfMonth();
            $orderCount = fake()->numberBetween(8, 22);

            Order::factory()
                ->count($orderCount)
                ->create([
                    'customer_id' => $customers->random()->id,
                    'placed_at' => fake()->dateTimeBetween($month, $month->copy()->endOfMonth()),
                    'status' => fake()->randomElement([
                        OrderStatus::Delivered,
                        OrderStatus::Shipped,
                        OrderStatus::Processing,
                        OrderStatus::Pending,
                    ]),
                ])
                ->each(function (Order $order) use ($products): void {
                    $items = $products->random(fake()->numberBetween(1, 3));

                    $subtotal = 0;

                    foreach ($items as $product) {
                        $quantity = fake()->numberBetween(1, 2);
                        $lineTotal = $product->price_cents * $quantity;
                        $subtotal += $lineTotal;

                        OrderItem::factory()->create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_price_cents' => $product->price_cents,
                            'line_total_cents' => $lineTotal,
                        ]);
                    }

                    $order->update([
                        'subtotal_cents' => $subtotal,
                        'total_cents' => $subtotal,
                    ]);
                });
        }

        Order::factory()
            ->count(6)
            ->today()
            ->create(['customer_id' => $customers->random()->id])
            ->each(function (Order $order) use ($products): void {
                $product = $products->random();
                $quantity = fake()->numberBetween(1, 2);
                $lineTotal = $product->price_cents * $quantity;

                OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price_cents' => $product->price_cents,
                    'line_total_cents' => $lineTotal,
                ]);

                $order->update([
                    'subtotal_cents' => $lineTotal,
                    'total_cents' => $lineTotal,
                    'status' => fake()->randomElement(OrderStatus::cases()),
                ]);
            });

        Order::factory()
            ->count(5)
            ->pending()
            ->create([
                'customer_id' => $customers->random()->id,
                'placed_at' => now()->subDays(fake()->numberBetween(0, 3)),
            ])
            ->each(function (Order $order) use ($products): void {
                $product = $products->random();
                $lineTotal = $product->price_cents;

                OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price_cents' => $product->price_cents,
                    'line_total_cents' => $lineTotal,
                ]);

                $order->update([
                    'subtotal_cents' => $lineTotal,
                    'total_cents' => $lineTotal,
                ]);
            });

        $this->call(StorefrontDemoSeeder::class);
    }
}
