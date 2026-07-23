<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminOrderRepositoryInterface;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\OrderTimelineEvent;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private AdminOrderRepositoryInterface $orders,
        private NotificationService $notifications,
        private AuditService $audit,
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
            $previousStatus = $order->status;

            $this->orders->updateStatus($order, $status->value);

            $this->orders->createTimelineEvent($order, [
                'status' => $status->value,
                'message' => $message,
                'author_name' => $authorName ?: 'Admin',
            ]);

            $updatedOrder = $this->orders->find($order->id);

            $this->notifications->notifyOrderStatusChange(
                $updatedOrder,
                $previousStatus,
                $status,
                $message,
            );

            $this->audit->logOrderStatusUpdated(
                $updatedOrder,
                $previousStatus,
                $status,
                $message,
            );

            return $updatedOrder;
        });
    }

    public function addNote(Order $order, string $body, ?string $authorName = null): OrderNote
    {
        $note = $this->orders->createNote($order, [
            'body' => $body,
            'author_name' => $authorName ?: 'Admin',
        ]);

        $this->audit->logOrderNoteAdded($order, $body);

        return $note;
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
