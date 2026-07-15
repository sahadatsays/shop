<?php

namespace App\Contracts\Repositories;

use App\DTOs\Admin\Dashboard\CustomerSummaryData;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function countTotal(): int;

    /**
     * @return Collection<int, CustomerSummaryData>
     */
    public function latest(int $limit = 5): Collection;
}
