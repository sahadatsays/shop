<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\Admin\Dashboard\LowStockProductData;
use App\DTOs\Admin\Dashboard\TopProductData;
use App\DTOs\Shop\ShopFilters;
use App\Enums\ProductSort;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\MoneyFormatter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
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

    public function paginateShopProducts(ShopFilters $filters): LengthAwarePaginator
    {
        return $this->applyShopFilters(Product::query(), $filters)
            ->with(['category', 'brand', 'images'])
            ->tap(fn (Builder $query) => $this->applyShopSort($query, $filters->sort))
            ->paginate($filters->perPage, ['*'], 'page', $filters->page)
            ->withQueryString();
    }

    public function shopCategoryFilters(?ShopFilters $filters = null): Collection
    {
        return Category::query()
            ->active()
            ->whereNull('parent_id')
            ->ordered()
            ->withCount(['products as count' => fn (Builder $query) => $this->applyShopFilters($query, $filters, exclude: ['categories'])])
            ->get(['id', 'name', 'slug', 'parent_id'])
            ->filter(fn (Category $category): bool => (int) $category->count > 0)
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'parent_id' => $category->parent_id,
                'count' => (int) $category->count,
            ]);
    }

    public function shopBrandFilters(?ShopFilters $filters = null): Collection
    {
        return Brand::query()
            ->active()
            ->ordered()
            ->withCount(['products as count' => fn (Builder $query) => $this->applyShopFilters($query, $filters, exclude: ['brands'])])
            ->get(['id', 'name', 'slug'])
            ->filter(fn (Brand $brand): bool => (int) $brand->count > 0)
            ->map(fn (Brand $brand): array => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'count' => (int) $brand->count,
            ]);
    }

    public function shopPriceRange(?ShopFilters $filters = null): array
    {
        $query = $this->applyShopFilters(Product::query(), $filters, exclude: ['minPriceCents', 'maxPriceCents']);

        $range = $query
            ->selectRaw('MIN(price_cents) as min_cents, MAX(price_cents) as max_cents')
            ->first();

        return [
            'min_cents' => (int) ($range->min_cents ?? 0),
            'max_cents' => (int) ($range->max_cents ?? 0),
        ];
    }

    /**
     * @param  Builder<Product>  $query
     * @param  array<int, string>  $exclude
     * @return Builder<Product>
     */
    private function applyShopFilters(Builder $query, ?ShopFilters $filters, array $exclude = []): Builder
    {
        $query->shopVisible();

        if ($filters === null) {
            if (config('shop.require_in_stock', true)) {
                $query->inStock();
            }

            return $query;
        }

        if ($filters->inStock && config('shop.require_in_stock', true) && ! in_array('inStock', $exclude, true)) {
            $query->inStock();
        }

        if ($filters->search && ! in_array('search', $exclude, true)) {
            $term = '%'.$filters->search.'%';
            $query->where(function (Builder $builder) use ($term): void {
                $builder->where('products.name', 'like', $term)
                    ->orWhere('products.sku', 'like', $term)
                    ->orWhereHas('brand', fn (Builder $brand) => $brand->where('name', 'like', $term))
                    ->orWhereHas('category', fn (Builder $category) => $category->where('name', 'like', $term));
            });
        }

        if ($filters->categories !== [] && ! in_array('categories', $exclude, true)) {
            $query->whereHas('category', fn (Builder $category) => $category->whereIn('slug', $filters->categories));
        }

        if ($filters->brands !== [] && ! in_array('brands', $exclude, true)) {
            $query->whereHas('brand', fn (Builder $brand) => $brand->whereIn('slug', $filters->brands));
        }

        if ($filters->minPriceCents !== null && ! in_array('minPriceCents', $exclude, true)) {
            $query->where('price_cents', '>=', $filters->minPriceCents);
        }

        if ($filters->maxPriceCents !== null && ! in_array('maxPriceCents', $exclude, true)) {
            $query->where('price_cents', '<=', $filters->maxPriceCents);
        }

        if ($filters->featured) {
            $query->featured();
        }

        if ($filters->onSale) {
            $query->onSale();
        }

        if ($filters->newArrival) {
            $query->newArrival();
        }

        return $query;
    }

    /**
     * @param  Builder<Product>  $query
     */
    private function applyShopSort(Builder $query, ProductSort $sort): void
    {
        match ($sort) {
            ProductSort::Newest => $query->latest('created_at'),
            ProductSort::Oldest => $query->oldest('created_at'),
            ProductSort::PriceLow => $query->orderBy('price_cents'),
            ProductSort::PriceHigh => $query->orderByDesc('price_cents'),
            ProductSort::NameAsc => $query->orderBy('name'),
            ProductSort::NameDesc => $query->orderByDesc('name'),
            ProductSort::BestSelling => $query
                ->withSum('orderItems as units_sold', 'quantity')
                ->orderByDesc('units_sold')
                ->orderByDesc('is_featured'),
            ProductSort::MostViewed => $query->latest('created_at'),
            ProductSort::Featured => $query
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('name'),
        };
    }
}
