<?php

namespace App\Contracts\Repositories;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AdminBrandRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Brand;

    public function create(array $attributes): Brand;

    public function update(Brand $brand, array $attributes): Brand;

    public function delete(Brand $brand): bool;

    public function restore(int $id): Brand;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;

    public function countActive(): int;

    /**
     * @return Collection<int, Brand>
     */
    public function featured(int $limit = 6): Collection;
}
