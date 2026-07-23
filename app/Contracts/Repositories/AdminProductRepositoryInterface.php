<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AdminProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Product;

    public function create(array $attributes): Product;

    public function update(Product $product, array $attributes): Product;

    public function delete(Product $product): bool;

    public function restore(int $id): Product;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;

    public function skuExists(string $sku, ?int $ignoreId = null): bool;

    /**
     * @return Collection<int, Product>
     */
    public function options(?int $excludeId = null): Collection;
}
