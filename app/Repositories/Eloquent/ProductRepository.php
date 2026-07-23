<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\Admin\Dashboard\LowStockProductData;
use App\DTOs\Admin\Dashboard\TopProductData;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\MoneyFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function countActive(): int
    {
        return Product::query()->active()->count();
    }

    public function countLowStock(): int
    {
        return Product::query()->active()->lowStock()->count();
    }

    public function topSelling(int $limit = 5): Collection
    {
        $aggregates = OrderItem::query()
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as units_sold'),
                DB::raw('SUM(line_total_cents) as revenue_cents'),
            ])
            ->groupBy('product_id')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get();

        $products = Product::query()
            ->with('category')
            ->whereIn('id', $aggregates->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return $aggregates->map(function ($row) use ($products): TopProductData {
            $product = $products->get($row->product_id);

            return new TopProductData(
                name: $product->name,
                category: $product->category->name,
                unitsSold: (int) $row->units_sold,
                revenueFormatted: MoneyFormatter::format((int) $row->revenue_cents),
            );
        });
    }

    public function lowStock(int $limit = 5): Collection
    {
        return Product::query()
            ->active()
            ->lowStock()
            ->with('category')
            ->orderBy('stock_quantity')
            ->limit($limit)
            ->get()
            ->map(fn (Product $product): LowStockProductData => new LowStockProductData(
                productId: $product->id,
                name: $product->name,
                stockQuantity: $product->stock_quantity,
                threshold: $product->low_stock_threshold,
            ));
    }

    public function inventoryStatusCounts(): array
    {
        $products = Product::query()->active()->get(['stock_quantity', 'low_stock_threshold']);

        return [
            'in_stock' => $products->filter(fn (Product $p): bool => $p->stock_quantity > $p->low_stock_threshold)->count(),
            'low_stock' => $products->filter(fn (Product $p): bool => $p->stock_quantity > 0 && $p->stock_quantity <= $p->low_stock_threshold)->count(),
            'out_of_stock' => $products->filter(fn (Product $p): bool => $p->stock_quantity === 0)->count(),
        ];
    }
}
