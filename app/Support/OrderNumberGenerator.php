<?php

namespace App\Support;

use App\Models\Order;

class OrderNumberGenerator
{
    public static function generate(): string
    {
        do {
            $number = 'VS-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
