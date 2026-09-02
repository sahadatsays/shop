<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case PartiallyRefunded = 'partially_refunded';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::PartiallyRefunded => 'Partially refunded',
            self::Refunded => 'Refunded',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::PartiallyRefunded => 'warning',
            self::Refunded => 'muted',
        };
    }

    public function storefrontLabel(): string
    {
        return match ($this) {
            self::Pending => 'Payment pending',
            self::Paid => 'Paid',
            self::PartiallyRefunded => 'Partially refunded',
            self::Refunded => 'Refunded',
        };
    }

    public function isRefundable(): bool
    {
        return in_array($this, [self::Paid, self::PartiallyRefunded], true);
    }
}
