<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\MoneyFormatter;
use App\Support\OrderTracking\OrderTimelineBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class CustomerOrderSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OrderTimelineBuilder $timelineBuilder */
        $timelineBuilder = app(OrderTimelineBuilder::class);

        return [
            'id' => $this->id,
            'number' => '#'.$this->order_number,
            'order_number' => $this->order_number,
            'placed' => $this->placed_at?->format('M j, Y'),
            'total' => MoneyFormatter::format($this->total_cents),
            'status' => $this->status->storefrontLabel(),
            'status_variant' => $this->status->storefrontBadgeVariant(),
            'status_group' => $this->status->storefrontFilterGroup(),
            'progress' => $timelineBuilder->compactProgress($this->resource),
            'eta' => $this->etaLabel(),
            'items' => $this->items->map(fn ($item): array => [
                'name' => $item->product?->name ?? 'Product',
                'image' => $item->product?->primaryImageUrl(),
                'url' => $item->product ? route('product.show', $item->product) : route('shop'),
            ])->all(),
            'track_url' => route('account.orders.show', $this->resource),
            'is_delivered' => $this->status === OrderStatus::Delivered,
        ];
    }

    private function etaLabel(): string
    {
        return match ($this->status) {
            OrderStatus::Delivered => 'Delivered '.$this->placed_at?->format('M j'),
            OrderStatus::Shipped => $this->estimated_delivery_at
                ? 'Arriving '.$this->estimated_delivery_at->format('D, M j')
                : 'In transit',
            OrderStatus::Cancelled => 'Order cancelled',
            OrderStatus::Returned => 'Returned',
            OrderStatus::Refunded => 'Refunded',
            default => 'Preparing your order',
        };
    }
}
