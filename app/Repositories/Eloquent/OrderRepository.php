<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\DTOs\Admin\Dashboard\RecentOrderData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\MoneyFormatter;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function countToday(): int
    {
        return Order::query()->placedToday()->count();
    }

    public function countPending(): int
    {
        return Order::query()->pending()->count();
    }

    public function sumRevenueTodayCents(): int
    {
        return (int) Order::query()
            ->placedToday()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->sum('total_cents');
    }

    public function sumRevenueCents(): int
    {
        return (int) Order::query()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->sum('total_cents');
    }

    public function monthlyMetrics(int $months = 12): Collection
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $orders = Order::query()
            ->where('placed_at', '>=', $start)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->get(['placed_at', 'total_cents']);

        $grouped = $orders->groupBy(fn (Order $order): string => $order->placed_at->format('Y-m'));

        return collect(range(0, $months - 1))
            ->map(function (int $offset) use ($start, $grouped): array {
                $date = $start->copy()->addMonths($offset);
                $key = $date->format('Y-m');
                $monthOrders = $grouped->get($key, collect());

                return [
                    'month' => $key,
                    'label' => $date->format('M'),
                    'sales_cents' => (int) $monthOrders->sum('total_cents'),
                    'order_count' => $monthOrders->count(),
                ];
            });
    }

    public function recent(int $limit = 5): Collection
    {
        return Order::query()
            ->with('customer')
            ->latest('placed_at')
            ->limit($limit)
            ->get()
            ->map(fn (Order $order): RecentOrderData => new RecentOrderData(
                orderNumber: $order->order_number,
                customerName: $order->customer->name,
                totalFormatted: MoneyFormatter::format($order->total_cents),
                status: $order->status->label(),
                statusVariant: self::statusVariant($order->status),
                placedAt: $order->placed_at->diffForHumans(),
            ));
    }

    private static function statusVariant(OrderStatus $status): string
    {
        return $status->badgeVariant();
    }
}
