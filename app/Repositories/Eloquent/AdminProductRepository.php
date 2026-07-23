<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\AdminProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminProductRepository implements AdminProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category', 'brand', 'images'])
            ->withCount('orderItems');

        if ($filters['trashed'] ?? false) {
            $query->onlyTrashed();
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($categoryId = $filters['category_id'] ?? null) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId = $filters['brand_id'] ?? null) {
            $query->where('brand_id', $brandId);
        }

        if ($filters['featured'] ?? false) {
            $query->featured();
        }

        if ($filters['new_arrival'] ?? false) {
            $query->newArrival();
        }

        return $query->ordered()->paginate($perPage)->withQueryString();
    }

    public function find(int $id): Product
    {
        return Product::query()
            ->with(['category', 'brand', 'images', 'specifications', 'attributes', 'relatedProducts'])
            ->withCount('orderItems')
            ->findOrFail($id);
    }

    public function create(array $attributes): Product
    {
        return Product::query()->create($attributes);
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->update($attributes);

        return $product->fresh(['category', 'brand', 'images'])->loadCount('orderItems');
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function restore(int $id): Product
    {
        $product = Product::query()->onlyTrashed()->findOrFail($id);
        $product->restore();

        return $product->fresh(['category', 'brand', 'images'])->loadCount('orderItems');
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Product::query()
            ->withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    public function skuExists(string $sku, ?int $ignoreId = null): bool
    {
        return Product::query()
            ->withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('sku', $sku)
            ->exists();
    }

    public function options(?int $excludeId = null): Collection
    {
        return Product::query()
            ->published()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->ordered()
            ->get(['id', 'name', 'sku']);
    }

    public function countPublished(): int
    {
        return Product::query()->published()->count();
    }
}
