<?php

namespace App\Contracts\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AdminCategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Category;

    public function findTrashed(int $id): Category;

    public function create(array $attributes): Category;

    public function update(Category $category, array $attributes): Category;

    public function delete(Category $category): bool;

    public function restore(int $id): Category;

    /**
     * @return Collection<int, Category>
     */
    public function parentOptions(?int $excludeId = null): Collection;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;
}
