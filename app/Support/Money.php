<?php

namespace App\Support;

/**
 * Money amounts in config/UI are whole currency units (e.g. 20 = ৳20).
 * Persistence and calculations still use integer minor units (paisa/cents).
 */
class Money
{
    public static function toMinor(int|float|string|null $amount): int
    {
        if ($amount === null || $amount === '') {
            return 0;
        }

        return (int) round(((float) $amount) * 100);
    }

    public static function toAmount(int $minorUnits): float
    {
        return round($minorUnits / 100, 2);
    }

    public static function configAmount(string $key, int|float $default = 0): int
    {
        return self::toMinor(config($key, $default));
    }
}
