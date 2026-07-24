<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Http\Resources\CustomerOrderSummaryResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Support\MoneyFormatter;
use App\Support\OrderTracking\OrderTimelineBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomerDashboardService
{
    public function __construct(
        private CustomerRewardsService $rewards,
        private OrderTrackingService $tracking,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function data(Customer $customer): array
    {
        $orders = $this->tracking->listForCustomer($customer);
        $rewards = $this->rewards->summary($orders);
        $spotlightOrder = $this->spotlightOrder($orders);
        $spotlightTracking = $spotlightOrder ? $this->spotlightTracking($spotlightOrder) : null;

        return [
            'welcome' => $this->welcome($customer, $orders, $rewards, $spotlightOrder, $spotlightTracking),
            'stats' => $this->stats($orders, $rewards),
            'recentOrders' => $this->recentOrders($orders),
            'spotlightOrder' => $spotlightTracking,
            'rewards' => $rewards,
            'quickActions' => $this->quickActions($spotlightTracking),
            'recommendedProducts' => $this->recommendedProducts($customer, $orders),
        ];
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  array<string, mixed>  $rewards
     * @param  array<string, mixed>|null  $spotlightTracking
     * @return array<string, mixed>
     */
    private function welcome(
        Customer $customer,
        Collection $orders,
        array $rewards,
        ?Order $spotlightOrder,
        ?array $spotlightTracking,
    ): array {
        $firstName = Str::before(trim($customer->name), ' ') ?: $customer->name;

        $message = 'Explore our latest gear and earn rewards on every purchase.';
        $highlight = null;
        $trackUrl = route('account.orders');
        $showTrackCta = false;

        if ($spotlightOrder && $spotlightTracking) {
            $showTrackCta = true;
            $trackUrl = route('account.orders.show', $spotlightOrder);
            $leadItem = $spotlightOrder->items->first()?->product?->name ?? 'your order';

            $message = match ($spotlightOrder->status) {
                OrderStatus::Shipped => sprintf(
                    'Your %s is on the move%s.',
                    $leadItem,
                    $spotlightOrder->estimated_delivery_at
                        ? ' — arriving '.$spotlightOrder->estimated_delivery_at->format('l')
                        : '',
                ),
                OrderStatus::Delivered => sprintf('Your %s was delivered. Hope you love it.', $leadItem),
                default => sprintf('Your order %s is being prepared.', '#'.$spotlightOrder->order_number),
            };
        } elseif ($rewards['points_to_next_tier']) {
            $highlight = number_format($rewards['points_to_next_tier']).' points';
            $message = sprintf(
                'You\'re %s away from %s.',
                $highlight,
                $rewards['next_tier'],
            );
        } elseif ($orders->isNotEmpty()) {
            $message = 'Thanks for shopping with Valor. Browse new arrivals picked for you below.';
        }

        return [
            'date_label' => now()->format('l, F j'),
            'headline' => 'Welcome back, '.$firstName,
            'message' => $message,
            'highlight' => $highlight,
            'track_url' => $trackUrl,
            'show_track_cta' => $showTrackCta,
            'shop_url' => route('shop', ['sort' => 'newest']),
        ];
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  array<string, mixed>  $rewards
     * @return list<array<string, mixed>>
     */
    private function stats(Collection $orders, array $rewards): array
    {
        $quarterStart = now()->startOfQuarter();
        $ordersThisQuarter = $orders->filter(
            fn (Order $order): bool => $order->placed_at !== null && $order->placed_at->gte($quarterStart),
        )->count();

        $inTransit = $orders->where('status', OrderStatus::Shipped)->count();
        $totalSavedCents = (int) $orders->sum('discount_cents');

        return [
            [
                'label' => 'Total orders',
                'value' => (string) $orders->count(),
                'trend' => $ordersThisQuarter > 0
                    ? '+'.$ordersThisQuarter.' this quarter'
                    : 'No orders this quarter',
                'up' => $ordersThisQuarter > 0,
                'icon' => 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7ZM9 10V6a3 3 0 0 1 6 0v4',
                'tone' => 'bg-navy-900 text-bronze-400',
            ],
            [
                'label' => 'In transit',
                'value' => (string) $inTransit,
                'trend' => $inTransit > 0 ? 'Arriving this week' : 'Nothing shipping yet',
                'up' => null,
                'icon' => 'M3 7h11v8H3zM14 10h4l3 3v2h-7zM7 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm11 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z',
                'tone' => 'bg-olive-600 text-white',
            ],
            [
                'label' => 'Reward points',
                'value' => $rewards['points_label'],
                'trend' => $rewards['last_order_points_label'],
                'up' => $rewards['last_order_points'] > 0,
                'icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z',
                'tone' => 'bg-bronze-500 text-white',
            ],
            [
                'label' => 'Total saved',
                'value' => MoneyFormatter::format($totalSavedCents),
                'trend' => 'With member pricing',
                'up' => $totalSavedCents > 0,
                'icon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm-2-12h3.5a1.5 1.5 0 0 1 0 3H10m1-4v6',
                'tone' => 'bg-navy-100 text-navy-700',
            ],
        ];
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return list<array<string, mixed>>
     */
    private function recentOrders(Collection $orders): array
    {
        return CustomerOrderSummaryResource::collection(
            $orders->take(4),
        )->resolve();
    }

    /**
     * @param  Collection<int, Order>  $orders
     */
    private function spotlightOrder(Collection $orders): ?Order
    {
        $active = $orders->filter(fn (Order $order): bool => ! in_array($order->status, [
            OrderStatus::Cancelled,
            OrderStatus::Returned,
            OrderStatus::Refunded,
        ], true));

        return $active->firstWhere('status', OrderStatus::Shipped)
            ?? $active->firstWhere('status', OrderStatus::Delivered)
            ?? $active->first();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function spotlightTracking(Order $order): ?array
    {
        $trackable = $this->tracking->show($order);

        /** @var OrderTimelineBuilder $timelineBuilder */
        $timelineBuilder = app(OrderTimelineBuilder::class);

        return [
            'number' => '#'.$trackable->order_number,
            'order_number' => $trackable->order_number,
            'status' => $trackable->status->storefrontLabel(),
            'track_url' => route('account.orders.show', $trackable),
            'timeline' => $timelineBuilder->build($trackable),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $spotlightTracking
     * @return list<array<string, string|null>>
     */
    private function quickActions(?array $spotlightTracking): array
    {
        return [
            [
                'label' => 'Track order',
                'href' => $spotlightTracking['track_url'] ?? route('track-order.create'),
                'icon' => 'M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
            ],
            [
                'label' => 'Start a return',
                'href' => route('support'),
                'icon' => 'M3 12a9 9 0 1 0 3-6.7M3 4v4h4',
            ],
            [
                'label' => 'Reorder favorites',
                'href' => route('wishlist'),
                'icon' => 'M17 2v4H7a4 4 0 0 0 0 8h1m-1 8v-4h10a4 4 0 0 0 0-8h-1',
            ],
            [
                'label' => 'Contact support',
                'href' => route('support'),
                'icon' => 'M21 12a9 9 0 1 0-3.5 7.1L21 21l-1-3.4A8.96 8.96 0 0 0 21 12ZM8 10h8M8 14h5',
            ],
            [
                'label' => 'Gift cards',
                'href' => route('shop'),
                'icon' => 'M3 8h18v4H3zM5 12v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8M12 8v13M12 8s-1.5-4-4-4a2 2 0 0 0 0 4h4Zm0 0s1.5-4 4-4a2 2 0 0 1 0 4h-4Z',
            ],
            [
                'label' => 'Size guide',
                'href' => route('support'),
                'icon' => 'M3 9h18v6H3zM7 9v3m4-3v2m4-2v3m4-3v2',
            ],
        ];
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return Collection<int, Product>
     */
    private function recommendedProducts(Customer $customer, Collection $orders): Collection
    {
        $purchasedProductIds = $orders
            ->flatMap(fn (Order $order) => $order->items->pluck('product_id'))
            ->filter()
            ->unique()
            ->values();

        $categoryIds = $orders
            ->flatMap(fn (Order $order) => $order->items->map(fn ($item) => $item->product?->category_id))
            ->filter()
            ->unique()
            ->values();

        $query = Product::query()
            ->published()
            ->with(['category', 'images'])
            ->when(
                $purchasedProductIds->isNotEmpty(),
                fn ($builder) => $builder->whereNotIn('id', $purchasedProductIds),
            );

        if ($categoryIds->isNotEmpty()) {
            $personalized = (clone $query)
                ->whereIn('category_id', $categoryIds)
                ->orderByDesc('is_featured')
                ->orderByDesc('is_new_arrival')
                ->limit(4)
                ->get();

            if ($personalized->count() >= 4) {
                return $personalized;
            }
        }

        $featured = (clone $query)
            ->featured()
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        if ($featured->isNotEmpty()) {
            return $featured;
        }

        return $query
            ->orderByDesc('is_new_arrival')
            ->orderBy('sort_order')
            ->limit(4)
            ->get();
    }
}
