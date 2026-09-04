<?php

namespace App\Support;

class ShippingRates
{
    public static function freeShippingThresholdCents(): int
    {
        $settings = StoreSettings::current();

        if ($settings->free_shipping_threshold_cents !== null) {
            return (int) $settings->free_shipping_threshold_cents;
        }

        return Money::toMinor(config('store.free_shipping_threshold_amount', 2000));
    }

    public static function flatShippingCents(): int
    {
        $settings = StoreSettings::current();

        if ($settings->flat_shipping_cents !== null) {
            return (int) $settings->flat_shipping_cents;
        }

        return Money::toMinor(config('store.flat_shipping_amount', 80));
    }

    public static function methodCostCents(string $method): int
    {
        $settings = StoreSettings::current();

        return match ($method) {
            'insideDhaka' => (int) ($settings->inside_dhaka_shipping_cents
                ?? Money::toMinor(config('store.inside_dhaka_shipping_amount', 60))),
            'outsideDhaka' => (int) ($settings->outside_dhaka_shipping_cents
                ?? Money::toMinor(config('store.outside_dhaka_shipping_amount', 120))),
            default => self::flatShippingCents(),
        };
    }

    /**
     * Cart estimate before a delivery method is chosen (threshold + flat rate).
     */
    public static function estimateForSubtotal(int $subtotalCents): int
    {
        return self::applyFreeShippingThreshold(self::flatShippingCents(), $subtotalCents);
    }

    /**
     * Resolve checkout shipping cost in minor units for a delivery method.
     */
    public static function resolve(string $method, int $subtotalCents): int
    {
        if (! is_array(config("cart.shipping_methods.{$method}"))) {
            return self::estimateForSubtotal($subtotalCents);
        }

        if (in_array($method, ['insideDhaka', 'outsideDhaka'], true)) {
            return self::methodCostCents($method);
        }

        return self::estimateForSubtotal($subtotalCents);
    }

    /**
     * @return array<string, array{label: string, description: string, cost_cents: int, cost_amount: float, price: string}>
     */
    public static function methodsForCheckout(int $subtotalCents = 0): array
    {
        $methods = [];

        foreach (config('cart.shipping_methods', []) as $key => $method) {
            $costCents = self::resolve($key, $subtotalCents);

            $methods[$key] = [
                'label' => $method['label'],
                'description' => $method['description'] ?? '',
                'cost_cents' => $costCents,
                'cost_amount' => Money::toAmount($costCents),
                'price' => $costCents === 0 ? 'Free' : MoneyFormatter::format($costCents),
            ];
        }

        return $methods;
    }

    private static function applyFreeShippingThreshold(int $flatShippingCents, int $subtotalCents): int
    {
        $threshold = self::freeShippingThresholdCents();

        if ($subtotalCents === 0 || $subtotalCents >= $threshold) {
            return 0;
        }

        return $flatShippingCents;
    }
}
