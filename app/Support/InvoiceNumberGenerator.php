<?php

namespace App\Support;

use App\Models\Invoice;

class InvoiceNumberGenerator
{
    public static function generate(?\DateTimeInterface $at = null): string
    {
        $year = ($at ?? now())->format('Y');
        $prefix = 'INV-'.$year.'-';

        $latest = Invoice::query()
            ->where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $sequence = 1;

        if (is_string($latest) && preg_match('/^INV-\d{4}-(\d+)$/', $latest, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        do {
            $number = $prefix.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Invoice::query()->where('invoice_number', $number)->exists());

        return $number;
    }
}
