<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminOrderRepositoryInterface;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\OrderTimelineEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private AdminOrderRepositoryInterface $orders,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->orders->paginate($filters);
    }

    /**
     * @return array{total: int, pending: int, shipped: int, revenue_cents: int}
     */
    public function summary(): array
    {
        return $this->orders->summary();
    }

    public function show(int $id): Order
    {
        return $this->orders->find($id);
    }

    public function updateStatus(Order $order, OrderStatus $status, ?string $message = null, ?string $authorName = null): Order
    {
        if ($order->status === $status) {
            return $order;
        }

        return DB::transaction(function () use ($order, $status, $message, $authorName): Order {
            $this->orders->updateStatus($order, $status->value);

            $this->orders->createTimelineEvent($order, [
                'status' => $status->value,
                'message' => $message,
                'author_name' => $authorName ?: 'Admin',
            ]);

            return $this->orders->find($order->id);
        });
    }

    public function addNote(Order $order, string $body, ?string $authorName = null): OrderNote
    {
        return $this->orders->createNote($order, [
            'body' => $body,
            'author_name' => $authorName ?: 'Admin',
        ]);
    }

    public function recordPlacedEvent(Order $order, ?string $authorName = null): OrderTimelineEvent
    {
        return $this->orders->createTimelineEvent($order, [
            'status' => $order->status->value,
            'message' => 'Order placed.',
            'author_name' => $authorName ?: 'System',
            'created_at' => $order->placed_at,
            'updated_at' => $order->placed_at,
        ]);
    }
}
