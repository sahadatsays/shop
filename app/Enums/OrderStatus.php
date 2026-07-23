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
    case RefundReady = 'refund_ready';

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
            self::RefundReady => 'Refund Ready',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Delivered, self::Shipped => 'success',
            self::Processing, self::Packed, self::Confirmed => 'brand',
            self::Pending => 'warning',
            self::Cancelled, self::Returned => 'danger',
            self::RefundReady => 'muted',
        };
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
}
