<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberGenerator
{
    public static function generate(): string
    {
        do {
            $number = 'VS-'.Str::upper(Str::random(12));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
