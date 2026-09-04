<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Returned = 'returned';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Packed => 'Packed',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
            self::Returned => 'Returned',
            self::Refunded => 'Refunded',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Delivered, self::Shipped => 'success',
            self::Processing, self::Packed, self::Confirmed => 'brand',
            self::Pending => 'warning',
            self::Cancelled, self::Returned => 'danger',
            self::Refunded => 'muted',
        };
    }

    public function storefrontBadgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'bronze',
            self::Confirmed => 'navy',
            self::Processing => 'navy',
            self::Packed => 'navy',
            self::Shipped => 'bronze',
            self::Delivered => 'olive',
            self::Cancelled => 'danger',
            self::Returned => 'bronze',
            self::Refunded => 'neutral',
        };
    }

    public function storefrontLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Packed => 'Packed',
            self::Shipped => 'In transit',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
            self::Returned => 'Returned',
            self::Refunded => 'Refunded',
        };
    }

    public function storefrontFilterGroup(): ?string
    {
        return match ($this) {
            self::Pending, self::Confirmed, self::Processing, self::Packed => 'Processing',
            self::Shipped => 'In transit',
            self::Delivered => 'Delivered',
            default => null,
        };
    }

    public function canTransitionTo(self $status): bool
    {
        if ($this === $status) {
            return false;
        }

        if ($this === self::Refunded) {
            return false;
        }

        if ($this === self::Delivered) {
            return in_array($status, [self::Returned, self::Refunded], true);
        }

        if ($this === self::Cancelled) {
            return $status === self::Refunded;
        }

        if ($this === self::Returned) {
            return $status === self::Refunded;
        }

        if (in_array($status, [self::Cancelled, self::Returned, self::Refunded], true)) {
            return ! in_array($this, [self::Delivered, self::Cancelled, self::Returned, self::Refunded], true);
        }

        $flow = self::progressFlow();
        $currentIndex = array_search($this, $flow, true);
        $targetIndex = array_search($status, $flow, true);

        if ($currentIndex === false || $targetIndex === false) {
            return false;
        }

        return $targetIndex > $currentIndex;
    }

    /**
     * @return array<int, self>
     */
    public static function progressFlow(): array
    {
        return [
            self::Pending,
            self::Confirmed,
            self::Processing,
            self::Packed,
            self::Shipped,
            self::Delivered,
        ];
    }

    /**
     * Immediate next status in the fulfillment progress flow, if any.
     */
    public function nextProgressStatus(): ?self
    {
        $flow = self::progressFlow();
        $currentIndex = array_search($this, $flow, true);

        if ($currentIndex === false) {
            return null;
        }

        return $flow[$currentIndex + 1] ?? null;
    }

    public function canAdvanceProgress(): bool
    {
        $next = $this->nextProgressStatus();

        return $next !== null && $this->canTransitionTo($next);
    }

    /**
     * @return array<int, self>
     */
    public static function pendingStatuses(): array
    {
        return [
            self::Pending,
            self::Confirmed,
            self::Processing,
            self::Packed,
        ];
    }

    /**
     * @return array<int, self>
     */
    public static function alternativeStatuses(): array
    {
        return [
            self::Cancelled,
            self::Returned,
            self::Refunded,
        ];
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled, self::Refunded], true);
    }
}
