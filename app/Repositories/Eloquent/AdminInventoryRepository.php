<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\AdminInventoryRepositoryInterface;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminInventoryRepository implements AdminInventoryRepositoryInterface
{
    public function paginateProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category', 'brand', 'warehouseStock.warehouse'])
            ->published();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        match ($filters['stock_status'] ?? null) {
            'low_stock' => $query->lowStock(),
            'out_of_stock' => $query->outOfStock(),
            'in_stock' => $query->inStock()->whereColumn('stock_quantity', '>', 'low_stock_threshold'),
            default => null,
        };

        if ($warehouseId = $filters['warehouse_id'] ?? null) {
            $query->whereHas('warehouseStock', fn ($q) => $q->where('warehouse_id', $warehouseId));
        }

        return $query->ordered()->paginate($perPage)->withQueryString();
    }

    public function paginateMovements(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = StockMovement::query()
            ->with(['product', 'warehouse'])
            ->latest();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->whereHas('product', fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%"))
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($type = $filters['type'] ?? null) {
            $query->where('type', $type);
        }

        if ($productId = $filters['product_id'] ?? null) {
            $query->where('product_id', $productId);
        }

        if ($warehouseId = $filters['warehouse_id'] ?? null) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findProduct(int $id): Product
    {
        return Product::query()
            ->with(['category', 'brand', 'warehouseStock.warehouse', 'stockMovements' => fn ($q) => $q->with('warehouse')->limit(10)])
            ->findOrFail($id);
    }

    public function summaryCounts(): array
    {
        $products = Product::query()->published()->get(['stock_quantity', 'low_stock_threshold']);

        return [
            'total' => $products->count(),
            'in_stock' => $products->filter(fn (Product $p): bool => $p->stock_quantity > $p->low_stock_threshold)->count(),
            'low_stock' => $products->filter(fn (Product $p): bool => $p->isLowStock())->count(),
            'out_of_stock' => $products->filter(fn (Product $p): bool => $p->isOutOfStock())->count(),
        ];
    }

    public function activeWarehouses(): Collection
    {
        return Warehouse::query()->active()->ordered()->get();
    }
}
