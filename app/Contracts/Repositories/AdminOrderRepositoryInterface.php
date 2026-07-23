<?php

namespace App\Contracts\Repositories;

use App\Models\Order;
use App\Models\OrderNote;
use App\Models\OrderTimelineEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminOrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Order;

    /**
     * @return array{total: int, pending: int, shipped: int, revenue_cents: int}
     */
    public function summary(): array;

    public function updateStatus(Order $order, string $status): Order;

    public function createTimelineEvent(Order $order, array $attributes): OrderTimelineEvent;

    public function createNote(Order $order, array $attributes): OrderNote;
}
