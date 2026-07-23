<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Delivered, self::Shipped => 'success',
            self::Processing, self::Pending => 'warning',
            self::Cancelled => 'danger',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function pendingStatuses(): array
    {
        return [self::Pending, self::Processing];
    }
}
