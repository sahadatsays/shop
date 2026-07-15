<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection<int, array{label: string, value: int}>
     */
    public function topByRevenue(int $limit = 6): Collection;
}
