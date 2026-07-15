<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        ]));

        $products = $categories->flatMap(function (Category $category): Collection {
            return Product::factory()
                ->count(4)
                ->sequence(
                    ['stock_quantity' => 45],
                    ['stock_quantity' => 28],
                    ['stock_quantity' => 6, 'low_stock_threshold' => 10],
                    ['stock_quantity' => 0],
                )
                ->create(['category_id' => $category->id]);
        });

        $customers = Customer::factory()->count(24)->create();

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
    }
}
