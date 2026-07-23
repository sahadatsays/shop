<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CustomerOrderRepositoryInterface;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomerOrderRepository implements CustomerOrderRepositoryInterface
{
    public function findByNumberAndEmail(string $orderNumber, string $email): ?Order
    {
        $normalizedNumber = $this->normalizeOrderNumber($orderNumber);

        return Order::query()
            ->where('order_number', $normalizedNumber)
            ->whereHas('customer', fn ($query) => $query->where('email', Str::lower($email)))
            ->first();
    }

    public function findForCustomer(Customer $customer, Order $order): Order
    {
        return Order::query()
            ->whereKey($order->getKey())
            ->where('customer_id', $customer->id)
            ->firstOrFail();
    }

    public function listForCustomer(Customer $customer, array $filters = []): Collection
    {
        $query = Order::query()
            ->where('customer_id', $customer->id)
            ->with([
                'items.product.images',
            ])
            ->latest('placed_at');

        if ($filter = $filters['status_group'] ?? null) {
            if ($filter === 'Processing') {
                $query->whereIn('status', array_map(
                    fn ($status) => $status->value,
                    OrderStatus::pendingStatuses(),
                ));
            } elseif ($filter === 'In transit') {
                $query->where('status', OrderStatus::Shipped->value);
            } elseif ($filter === 'Delivered') {
                $query->where('status', OrderStatus::Delivered->value);
            }
        }

        return $query->get();
    }

    public function findTrackable(Order $order): Order
    {
        return Order::query()
            ->whereKey($order->getKey())
            ->with([
                'customer',
                'items.product.images',
                'timelineEvents.changedBy',
            ])
            ->firstOrFail();
    }

    private function normalizeOrderNumber(string $orderNumber): string
    {
        $trimmed = trim($orderNumber);

        if (Str::startsWith(Str::upper($trimmed), 'VS-')) {
            return Str::upper($trimmed);
        }

        if (preg_match('/^\d+$/', $trimmed) === 1) {
            return 'VS-'.Str::padLeft($trimmed, 5, '0');
        }

        return Str::upper($trimmed);
    }
}
