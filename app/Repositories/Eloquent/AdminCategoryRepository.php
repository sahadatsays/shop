<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\AdminCategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminCategoryRepository implements AdminCategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Category::query()
            ->with(['parent'])
            ->withCount('products');

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

        return $query->ordered()->paginate($perPage)->withQueryString();
    }

    public function find(int $id): Category
    {
        return Category::query()
            ->with(['parent', 'children'])
            ->withCount('products')
            ->findOrFail($id);
    }

    public function findTrashed(int $id): Category
    {
        return Category::query()
            ->onlyTrashed()
            ->withCount('products')
            ->findOrFail($id);
    }

    public function create(array $attributes): Category
    {
        return Category::query()->create($attributes);
    }

    public function update(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category->fresh(['parent', 'children']);
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }

    public function restore(int $id): Category
    {
        $category = Category::query()->onlyTrashed()->findOrFail($id);
        $category->restore();

        return $category->fresh(['parent'])->loadCount('products');
    }

    public function parentOptions(?int $excludeId = null): Collection
    {
        return Category::query()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId)->where('parent_id', '!=', $excludeId))
            ->whereNull('parent_id')
            ->ordered()
            ->get(['id', 'name']);
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Category::query()
            ->withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }
}
