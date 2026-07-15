<?php

namespace App\Support;

class MoneyFormatter
{
    public static function format(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }

    public static function formatCompact(int $cents): string
    {
        $amount = $cents / 100;

        if ($amount >= 1000) {
            return '$'.number_format($amount / 1000, 1).'k';
        }

        return self::format($cents);
    }
}
