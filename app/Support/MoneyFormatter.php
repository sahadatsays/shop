<?php

namespace App\Support;

use NumberFormatter;
use App\Support\StoreSettings;

class MoneyFormatter
{
    /**
     * Format cents to a currency string using the provided currency code or the
     * store's configured currency.
     */
    public static function format(int $cents, ?string $currency = null): string
    {
        $currency = $currency ?? StoreSettings::current()->currency ?? 'USD';

        $amount = $cents / 100;

        // Try using the Intl NumberFormatter when available for proper currency formatting.
        if (class_exists(NumberFormatter::class)) {
            try {
                $locale = self::localeForCurrency($currency);
                $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
                $result = $formatter->formatCurrency($amount, $currency);
                if ($result !== false) {
                    return $result;
                }
            } catch (\Throwable) {
                // fall through to fallback
            }
        }

        // Fallback: simple symbol + number_format (USD symbol used as default)
        $symbol = self::symbolForCurrency($currency);
        return $symbol . number_format($amount, 2);
    }

    public static function formatCompact(int $cents, ?string $currency = null): string
    {
        $amount = $cents / 100;

        if ($amount >= 1000) {
            // Use compact notation and include currency symbol
            $compact = number_format($amount / 1000, 1) . 'k';
            $symbol = self::symbolForCurrency($currency ?? StoreSettings::current()->currency ?? 'USD');
            return $symbol . $compact;
        }

        return self::format($cents, $currency);
    }

    private static function localeForCurrency(string $currency): string
    {
        return match (strtoupper($currency)) {
            'BDT' => 'en_BD',
            'INR' => 'en_IN',
            'PKR' => 'en_PK',
            'EUR' => 'de_DE',
            'GBP' => 'en_GB',
            'CAD' => 'en_CA',
            'AUD' => 'en_AU',
            default => 'en_US',
        };
    }

    private static function symbolForCurrency(string $currency): string
    {
        return match (strtoupper($currency)) {
            'BDT' => '৳',
            'INR' => '₹',
            'PKR' => '₨',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'CA$',
            'AUD' => 'AU$',
            default => '$',
        };
    }
}
