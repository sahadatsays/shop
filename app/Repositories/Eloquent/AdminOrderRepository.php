<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\AdminOrderRepositoryInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\OrderTimelineEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminOrderRepository implements AdminOrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::query()->with('customer');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        return $query->latest('placed_at')->paginate($perPage)->withQueryString();
    }

    public function find(int $id): Order
    {
        return Order::query()
            ->with([
                'customer',
                'items.product',
                'timelineEvents' => fn ($query) => $query->oldest(),
                'notes',
                'refunds.processedBy',
            ])
            ->findOrFail($id);
    }

    /**
     * @return array{total: int, pending: int, shipped: int, revenue_cents: int}
     */
    public function summary(): array
    {
        return [
            'total' => Order::query()->count(),
            'pending' => Order::query()->pending()->count(),
            'shipped' => Order::query()->where('status', OrderStatus::Shipped)->count(),
            'revenue_cents' => (int) Order::query()
                ->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Refunded])
                ->whereNot('payment_status', PaymentStatus::Refunded->value)
                ->sum('total_cents'),
        ];
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh(['customer', 'items.product', 'timelineEvents', 'notes']);
    }

    public function createTimelineEvent(Order $order, array $attributes): OrderTimelineEvent
    {
        return $order->timelineEvents()->create($attributes);
    }

    public function createNote(Order $order, array $attributes): OrderNote
    {
        return $order->notes()->create($attributes);
    }
}
