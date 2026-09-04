<?php

namespace App\Enums;

enum AddressType: string
{
    case Shipping = 'shipping';

    public function label(): string
    {
        return match ($this) {
            self::Shipping => 'Shipping',
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
