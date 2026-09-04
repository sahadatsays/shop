<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Failed = 'failed';
    case PartiallyRefunded = 'partially_refunded';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PartiallyPaid => 'Partially paid',
            self::Paid => 'Paid',
            self::Failed => 'Failed',
            self::PartiallyRefunded => 'Partially refunded',
            self::Refunded => 'Refunded',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending, self::PartiallyPaid, self::PartiallyRefunded => 'warning',
            self::Paid => 'success',
            self::Failed => 'danger',
            self::Refunded => 'muted',
        };
    }

    public function storefrontLabel(): string
    {
        return match ($this) {
            self::Pending => 'Payment pending',
            self::PartiallyPaid => 'Partially paid',
            self::Paid => 'Paid',
            self::Failed => 'Payment failed',
            self::PartiallyRefunded => 'Partially refunded',
            self::Refunded => 'Refunded',
        };
    }

    public function isRefundable(): bool
    {
        return in_array($this, [self::Paid, self::PartiallyPaid, self::PartiallyRefunded], true);
    }

    /**
     * Statuses that count toward order paid amount.
     *
     * @return list<self>
     */
    public static function collectedStatuses(): array
    {
        return [self::Paid, self::PartiallyRefunded];
    }
}
