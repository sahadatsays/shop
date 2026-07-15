<?php

namespace App\Contracts\Repositories;

use App\DTOs\Admin\Dashboard\LowStockProductData;
use App\DTOs\Admin\Dashboard\TopProductData;
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
}
