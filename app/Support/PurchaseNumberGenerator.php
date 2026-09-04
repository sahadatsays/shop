<?php

namespace App\Support;

use App\Models\Purchase;

class PurchaseNumberGenerator
{
    public static function generate(?\DateTimeInterface $at = null): string
    {
        $year = ($at ?? now())->format('Y');
        $prefix = 'PUR-'.$year.'-';

        $latest = Purchase::query()
            ->withTrashed()
            ->where('purchase_number', 'like', $prefix.'%')
            ->orderByDesc('purchase_number')
            ->value('purchase_number');

        $sequence = 1;

        if (is_string($latest) && preg_match('/^PUR-\d{4}-(\d+)$/', $latest, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        do {
            $number = $prefix.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            $sequence++;
        } while (
            Purchase::query()
                ->withTrashed()
                ->where('purchase_number', $number)
                ->exists()
        );

        return $number;
    }
}
