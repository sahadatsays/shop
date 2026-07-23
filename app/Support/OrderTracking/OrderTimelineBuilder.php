<?php

namespace App\Support\OrderTracking;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderTimelineEvent;

class OrderTimelineBuilder
{
    /**
     * @return array<int, array{label: string, meta: string, state: string}>
     */
    public function build(Order $order): array
    {
        if ($order->status === OrderStatus::Cancelled) {
            return $this->buildAlternativeTimeline($order, OrderStatus::Cancelled, 'Order cancelled.');
        }

        if ($order->status === OrderStatus::Returned) {
            return $this->buildAlternativeTimeline($order, OrderStatus::Returned, 'Order returned.');
        }

        if ($order->status === OrderStatus::Refunded) {
            return $this->buildAlternativeTimeline($order, OrderStatus::Refunded, 'Refund processed.');
        }

        $events = $order->timelineEvents
            ->sortBy('created_at')
            ->keyBy(fn (OrderTimelineEvent $event): string => $event->status->value);

        $steps = [];
        $currentReached = false;

        foreach (OrderStatus::progressFlow() as $status) {
            /** @var OrderTimelineEvent|null $event */
            $event = $events->get($status->value);
            $state = 'upcoming';

            if ($event !== null) {
                $state = $order->status === $status ? 'current' : 'done';
                $currentReached = $order->status === $status;
            } elseif (! $currentReached && $this->statusIndex($order->status) > $this->statusIndex($status)) {
                $state = 'done';
            } elseif ($order->status === $status) {
                $state = 'current';
                $currentReached = true;
            }

            if ($state === 'upcoming' && $this->statusIndex($order->status) > $this->statusIndex($status)) {
                $state = 'done';
            }

            if ($order->status === $status) {
                $state = 'current';
            } elseif ($this->statusIndex($order->status) > $this->statusIndex($status)) {
                $state = 'done';
            }

            $steps[] = [
                'label' => $this->stepLabel($status),
                'meta' => $this->stepMeta($status, $event, $order),
                'state' => $state,
            ];
        }

        return $steps;
    }

    /**
     * @return array<int, array{label: string, meta: string, state: string, updated_by?: string, note?: string|null}>
     */
    public function buildDetailed(Order $order): array
    {
        return $order->timelineEvents
            ->sortBy('created_at')
            ->map(fn (OrderTimelineEvent $event): array => [
                'label' => $event->status->label(),
                'meta' => $event->created_at?->format('M j, Y g:i A') ?? '—',
                'state' => 'done',
                'updated_by' => $event->updatedByLabel(),
                'note' => $event->message,
            ])
            ->values()
            ->all();
    }

    public function progressPercent(Order $order): int
    {
        $flow = OrderStatus::progressFlow();
        $index = array_search($order->status, $flow, true);

        if ($index === false) {
            return $order->status === OrderStatus::Delivered ? 100 : 0;
        }

        return (int) round((($index + 1) / count($flow)) * 100);
    }

    public function progressStep(Order $order): int
    {
        $flow = OrderStatus::progressFlow();
        $index = array_search($order->status, $flow, true);

        return $index === false ? 0 : $index + 1;
    }

    public function compactProgress(Order $order): int
    {
        return match ($order->status) {
            OrderStatus::Pending, OrderStatus::Confirmed => 1,
            OrderStatus::Processing, OrderStatus::Packed => 2,
            OrderStatus::Shipped => 3,
            OrderStatus::Delivered => 4,
            default => 0,
        };
    }

    /**
     * @return array<int, array{label: string, meta: string, state: string}>
     */
    private function buildAlternativeTimeline(Order $order, OrderStatus $status, string $fallbackMessage): array
    {
        $event = $order->timelineEvents
            ->sortByDesc('created_at')
            ->first(fn (OrderTimelineEvent $timelineEvent): bool => $timelineEvent->status === $status);

        return [[
            'label' => $status->label(),
            'meta' => $event
                ? $this->formatEventMeta($event)
                : $fallbackMessage,
            'state' => 'current',
        ]];
    }

    private function stepLabel(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Pending => 'Order placed',
            OrderStatus::Shipped => 'Shipped',
            OrderStatus::Delivered => 'Delivered',
            default => $status->label(),
        };
    }

    private function stepMeta(OrderStatus $status, ?OrderTimelineEvent $event, Order $order): string
    {
        if ($event !== null) {
            return $this->formatEventMeta($event);
        }

        if ($status === OrderStatus::Delivered && $order->estimated_delivery_at) {
            return 'Estimated '.$order->estimated_delivery_at->format('M j, Y');
        }

        return match ($status) {
            OrderStatus::Pending => 'Awaiting confirmation',
            OrderStatus::Delivered => 'Estimated delivery pending',
            default => 'Pending update',
        };
    }

    private function formatEventMeta(OrderTimelineEvent $event): string
    {
        $timestamp = $event->created_at?->format('M j, g:i A') ?? 'Recently';
        $author = $event->updatedByLabel();

        return $event->message
            ? "{$timestamp} — {$event->message}"
            : "{$timestamp} — {$author}";
    }

    private function statusIndex(OrderStatus $status): int
    {
        $index = array_search($status, OrderStatus::progressFlow(), true);

        return $index === false ? -1 : $index;
    }
}
