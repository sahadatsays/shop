<?php

namespace App\Enums;

enum PurchasePaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Unpaid',
            self::Partial => 'Partial',
            self::Paid => 'Paid',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Unpaid => 'warning',
            self::Partial => 'brand',
            self::Paid => 'success',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
