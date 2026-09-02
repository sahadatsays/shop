<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Support\MoneyFormatter;
use App\Support\OrderTracking\OrderTimelineBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderTrackingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OrderTimelineBuilder $timelineBuilder */
        $timelineBuilder = app(OrderTimelineBuilder::class);

        $shippingAddress = $this->shipping_address ?? [];
        $billingAddress = $this->billing_address ?? $shippingAddress;

        return [
            'order_number' => $this->order_number,
            'order_number_display' => '#'.$this->order_number,
            'placed_at' => $this->placed_at?->format('M j, Y'),
            'placed_at_full' => $this->placed_at?->format('M j, Y g:i A'),
            'status' => $this->status->storefrontLabel(),
            'status_variant' => $this->status->storefrontBadgeVariant(),
            'status_enum' => $this->status->value,
            'is_delivered' => $this->status === OrderStatus::Delivered,
            'can_request_return' => $this->canRequestReturn(),
            'return_window_days' => config('refunds.return_window_days', 30),
            'return_requested_at' => $this->return_requested_at?->format('M j, Y'),
            'payment_status' => ($this->payment_status instanceof PaymentStatus
                ? $this->payment_status
                : PaymentStatus::tryFrom((string) ($this->payment_status ?? 'paid')) ?? PaymentStatus::Paid
            )->storefrontLabel(),
            'payment_status_variant' => ($this->payment_status instanceof PaymentStatus
                ? $this->payment_status
                : PaymentStatus::tryFrom((string) ($this->payment_status ?? 'paid')) ?? PaymentStatus::Paid
            ) === PaymentStatus::Refunded ? 'neutral' : 'olive',
            'refunded_amount' => $this->refunded_cents > 0
                ? MoneyFormatter::format($this->refunded_cents)
                : null,
            'refundable_amount' => MoneyFormatter::format($this->refundableCents()),
            'payment_method' => $this->payment_method ?? 'Card',
            'estimated_delivery' => $this->estimatedDeliveryLabel(),
            'eta_heading' => $this->etaHeading(),
            'eta_detail' => $this->etaDetail(),
            'progress_percent' => $timelineBuilder->progressPercent($this->resource),
            'progress_step' => $timelineBuilder->progressStep($this->resource),
            'progress_total' => count(OrderStatus::progressFlow()),
            'timeline' => $timelineBuilder->build($this->resource),
            'timeline_detailed' => $timelineBuilder->buildDetailed($this->resource),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'item_count' => $this->items->sum('quantity'),
            'courier_name' => $this->courier_name,
            'tracking_number' => $this->tracking_number,
            'tracking_number_display' => $this->tracking_number_display,
            'delivery_instructions' => $this->delivery_instructions,
            'shipping_address' => $this->formatAddress($shippingAddress),
            'billing_address' => $this->formatAddress($billingAddress),
            'customer_name' => $this->customer?->name,
            'summary' => [
                'subtotal' => MoneyFormatter::format($this->subtotal_cents),
                'discount' => MoneyFormatter::format($this->discount_cents ?? 0),
                'shipping' => MoneyFormatter::format($this->shipping_cents ?? 0),
                'tax' => MoneyFormatter::format($this->tax_cents ?? 0),
                'total' => MoneyFormatter::format($this->total_cents),
            ],
        ];
    }

    private function estimatedDeliveryLabel(): ?string
    {
        if (! $this->estimated_delivery_at) {
            return null;
        }

        return $this->estimated_delivery_at->format('D, M j');
    }

    private function etaHeading(): string
    {
        return match ($this->status) {
            OrderStatus::Shipped => 'Out for delivery — estimated arrival',
            OrderStatus::Delivered => 'Delivered',
            OrderStatus::Cancelled => 'Order cancelled',
            OrderStatus::Returned => 'Order returned',
            OrderStatus::Refunded => 'Refund processed',
            default => 'Estimated delivery',
        };
    }

    private function etaDetail(): string
    {
        if ($this->status === OrderStatus::Delivered) {
            return $this->estimated_delivery_at?->format('M j, Y') ?? 'Delivery completed.';
        }

        if ($this->estimated_delivery_at) {
            return $this->estimated_delivery_at->format('D, M j').' · '.$this->status->storefrontLabel();
        }

        return 'We will share delivery timing once your order ships.';
    }

    /**
     * @param  array<string, mixed>  $address
     * @return array{name: string, lines: string, html: string}
     */
    private function formatAddress(array $address): array
    {
        $name = (string) ($address['name'] ?? $this->customer?->name ?? '');
        $line1 = (string) ($address['line1'] ?? '');
        $line2 = (string) ($address['line2'] ?? '');
        $city = (string) ($address['city'] ?? '');
        $state = (string) ($address['state'] ?? '');
        $postalCode = (string) ($address['postal_code'] ?? '');

        $cityLine = trim(collect([$city, $state])->filter()->implode(', ').' '.$postalCode);

        $lines = collect([$name, $line1, $line2, $cityLine])
            ->filter()
            ->values();

        return [
            'name' => e($name),
            'lines' => e($lines->implode("\n")),
            'html' => $lines->map(fn (string $line): string => e($line))->implode('<br>'),
        ];
    }
}
