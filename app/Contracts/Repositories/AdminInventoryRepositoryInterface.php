<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AdminInventoryRepositoryInterface
{
    public function paginateProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function paginateMovements(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findProduct(int $id): Product;

    /**
     * @return array{total: int, in_stock: int, low_stock: int, out_of_stock: int}
     */
    public function summaryCounts(): array;

    /**
     * @return Collection<int, Warehouse>
     */
    public function activeWarehouses(): Collection;
}
