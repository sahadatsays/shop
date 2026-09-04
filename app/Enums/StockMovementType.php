<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Initial = 'initial';
    case AdjustmentIn = 'adjustment_in';
    case AdjustmentOut = 'adjustment_out';
    case Recount = 'recount';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case Sale = 'sale';
    case Return = 'return';
    case Purchase = 'purchase';
    case PurchaseReturn = 'purchase_return';

    public function label(): string
    {
        return match ($this) {
            self::Initial => 'Initial stock',
            self::AdjustmentIn => 'Stock increase',
            self::AdjustmentOut => 'Stock decrease',
            self::Recount => 'Stock recount',
            self::TransferIn => 'Transfer in',
            self::TransferOut => 'Transfer out',
            self::Sale => 'Sale',
            self::Return => 'Return',
            self::Purchase => 'Purchase',
            self::PurchaseReturn => 'Purchase return',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Initial, self::AdjustmentIn, self::TransferIn, self::Return, self::Purchase => 'success',
            self::AdjustmentOut, self::TransferOut, self::Sale, self::PurchaseReturn => 'danger',
            self::Recount => 'info',
        };
    }

    public function isIncrease(): bool
    {
        return in_array($this, [
            self::Initial,
            self::AdjustmentIn,
            self::TransferIn,
            self::Return,
            self::Purchase,
        ], true);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<int, self>
     */
    public static function adjustable(): array
    {
        return [self::AdjustmentIn, self::AdjustmentOut, self::Recount];
    }
}
