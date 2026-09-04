<?php

namespace App\Enums;

enum RefundReason: string
{
    case CustomerReturn = 'customer_return';
    case OrderCancelled = 'order_cancelled';
    case DamagedItem = 'damaged_item';
    case DuplicateCharge = 'duplicate_charge';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CustomerReturn => 'Customer return',
            self::OrderCancelled => 'Order cancelled',
            self::DamagedItem => 'Damaged item',
            self::DuplicateCharge => 'Duplicate charge',
            self::Other => 'Other',
        };
    }
}
