<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\Shop\ShopFilters;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    public function getFilteredProducts(ShopFilters $filters): LengthAwarePaginator
    {
        return $this->products->paginateShopProducts($filters);
    }

    public function getCategories(?ShopFilters $filters = null): Collection
    {
        $cacheKey = 'shop.categories.'.md5(json_encode($filters?->queryParameters() ?? []));

        /** @var array<int, array{id: int, name: string, slug: string, count: int, parent_id: int|null}> $categories */
        $categories = Cache::remember(
            $cacheKey,
            (int) config('shop.category_cache_ttl', 3600),
            fn (): array => $this->products->shopCategoryFilters($filters)->values()->all(),
        );

        return collect($categories);
    }

    public function getBrands(?ShopFilters $filters = null): Collection
    {
        $cacheKey = 'shop.brands.'.md5(json_encode($filters?->queryParameters() ?? []));

        /** @var array<int, array{id: int, name: string, slug: string, count: int}> $brands */
        $brands = Cache::remember(
            $cacheKey,
            (int) config('shop.brand_cache_ttl', 3600),
            fn (): array => $this->products->shopBrandFilters($filters)->values()->all(),
        );

        return collect($brands);
    }

    /**
     * @return array{min_cents: int, max_cents: int, min_dollars: float, max_dollars: float}
     */
    public function getPriceRange(?ShopFilters $filters = null): array
    {
        $cacheKey = 'shop.price_range.'.md5(json_encode($filters?->queryParameters() ?? []));

        $range = Cache::remember(
            $cacheKey,
            (int) config('shop.price_range_cache_ttl', 3600),
            fn (): array => $this->products->shopPriceRange($filters),
        );

        return [
            ...$range,
            'min_dollars' => $range['min_cents'] / 100,
            'max_dollars' => $range['max_cents'] / 100,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function activeFilterLabels(ShopFilters $filters): array
    {
        $labels = [];

        foreach ($filters->categories as $slug) {
            $category = Category::query()->where('slug', $slug)->value('name');
            $labels[] = $category ?? $slug;
        }

        if ($filters->search) {
            $labels[] = 'Search: '.$filters->search;
        }

        if ($filters->featured) {
            $labels[] = 'Featured';
        }

        if ($filters->onSale) {
            $labels[] = 'On sale';
        }

        if ($filters->newArrival) {
            $labels[] = 'New arrivals';
        }

        if ($filters->minPriceCents !== null || $filters->maxPriceCents !== null) {
            $min = $filters->minPriceCents !== null ? '$'.number_format($filters->minPriceCents / 100, 0) : 'Any';
            $max = $filters->maxPriceCents !== null ? '$'.number_format($filters->maxPriceCents / 100, 0) : 'Any';
            $labels[] = "Price: {$min} – {$max}";
        }

        return $labels;
    }
}
