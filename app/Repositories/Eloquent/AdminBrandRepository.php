<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\AdminBrandRepositoryInterface;
use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminBrandRepository implements AdminBrandRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Brand::query()->withCount('products');

        if ($filters['trashed'] ?? false) {
            $query->onlyTrashed();
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($filters['featured'] ?? false) {
            $query->featured();
        }

        return $query->ordered()->paginate($perPage)->withQueryString();
    }

    public function find(int $id): Brand
    {
        return Brand::query()->withCount('products')->findOrFail($id);
    }

    public function create(array $attributes): Brand
    {
        return Brand::query()->create($attributes);
    }

    public function update(Brand $brand, array $attributes): Brand
    {
        $brand->update($attributes);

        return $brand->fresh()->loadCount('products');
    }

    public function delete(Brand $brand): bool
    {
        return (bool) $brand->delete();
    }

    public function restore(int $id): Brand
    {
        $brand = Brand::query()->onlyTrashed()->findOrFail($id);
        $brand->restore();

        return $brand->fresh()->loadCount('products');
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Brand::query()
            ->withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    public function countActive(): int
    {
        return Brand::query()->active()->count();
    }

    public function featured(int $limit = 6): Collection
    {
        return Brand::query()
            ->active()
            ->featured()
            ->withCount('products')
            ->ordered()
            ->limit($limit)
            ->get();
    }
}
