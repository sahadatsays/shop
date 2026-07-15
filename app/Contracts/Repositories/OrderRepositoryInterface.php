<?php

namespace App\Contracts\Repositories;

use App\DTOs\Admin\Dashboard\RecentOrderData;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function countToday(): int;

    public function countPending(): int;

    public function sumRevenueTodayCents(): int;

    public function sumRevenueCents(): int;

    /**
     * @return Collection<int, array{month: string, label: string, sales_cents: int, order_count: int}>
     */
    public function monthlyMetrics(int $months = 12): Collection;

    /**
     * @return Collection<int, RecentOrderData>
     */
    public function recent(int $limit = 5): Collection;
}
