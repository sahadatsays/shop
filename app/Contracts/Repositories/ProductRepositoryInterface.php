<?php

namespace App\Contracts\Repositories;

use App\DTOs\Admin\Dashboard\LowStockProductData;
use App\DTOs\Admin\Dashboard\TopProductData;
use App\DTOs\Shop\ShopFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function countActive(): int;

    public function countLowStock(): int;

    /**
     * @return Collection<int, TopProductData>
     */
    public function topSelling(int $limit = 5): Collection;

    /**
     * @return Collection<int, LowStockProductData>
     */
    public function lowStock(int $limit = 5): Collection;

    /**
     * @return array{in_stock: int, low_stock: int, out_of_stock: int}
     */
    public function inventoryStatusCounts(): array;

    public function paginateShopProducts(ShopFilters $filters): LengthAwarePaginator;

    /**
     * @return Collection<int, array{id: int, name: string, slug: string, count: int, parent_id: int|null}>
     */
    public function shopCategoryFilters(?ShopFilters $filters = null): Collection;

    /**
     * @return Collection<int, array{id: int, name: string, slug: string, count: int}>
     */
    public function shopBrandFilters(?ShopFilters $filters = null): Collection;

    /**
     * @return array{min_cents: int, max_cents: int}
     */
    public function shopPriceRange(?ShopFilters $filters = null): array;
}
