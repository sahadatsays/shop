<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Str;

class PaymentService
{
    public const METHOD_COD = 'cod';

    public const METHOD_CARD = 'card';

    public const METHOD_PAYPAL = 'paypal';

    public const METHOD_APPLE_PAY = 'applepay';

    /**
     * @return list<string>
     */
    public function availableMethods(): array
    {
        return [self::METHOD_COD];
    }

    /**
     * @return list<string>
     */
    public function comingSoonMethods(): array
    {
        return [self::METHOD_CARD, self::METHOD_PAYPAL, self::METHOD_APPLE_PAY];
    }

    public function isCashOnDelivery(string $method): bool
    {
        return $method === self::METHOD_COD;
    }

    public function labelFor(string $method): string
    {
        return match ($method) {
            self::METHOD_COD => 'Cash on delivery',
            self::METHOD_PAYPAL => 'PayPal',
            self::METHOD_APPLE_PAY => 'Apple Pay',
            default => 'Card',
        };
    }

    /**
     * Capture online payment or acknowledge COD (no capture).
     *
     * @return array{success: bool, reference: ?string, message: ?string, mark_paid: bool}
     */
    public function capture(Order $order, string $method): array
    {
        if ($order->payment_status !== PaymentStatus::Pending) {
            return [
                'success' => false,
                'reference' => null,
                'message' => 'This order payment has already been processed.',
                'mark_paid' => false,
            ];
        }

        if ($this->isCashOnDelivery($method)) {
            return [
                'success' => true,
                'reference' => 'COD-'.strtoupper(Str::random(10)),
                'message' => 'Order placed with cash on delivery. Payment remains pending until delivery.',
                'mark_paid' => false,
            ];
        }

        return [
            'success' => false,
            'reference' => null,
            'message' => 'Online payment is under construction. Please choose cash on delivery.',
            'mark_paid' => false,
        ];
    }

    /**
     * Issue a refund to the original payment method.
     *
     * @return array{success: bool, reference: ?string, message: ?string}
     */
    public function refund(Order $order, int $amountCents): array
    {
        if ($amountCents <= 0) {
            return [
                'success' => false,
                'reference' => null,
                'message' => 'Refund amount must be greater than zero.',
            ];
        }

        $paymentStatus = $order->payment_status instanceof PaymentStatus
            ? $order->payment_status
            : PaymentStatus::tryFrom((string) $order->payment_status) ?? PaymentStatus::Pending;

        if (! $paymentStatus->isRefundable()) {
            return [
                'success' => false,
                'reference' => null,
                'message' => 'This order payment is not eligible for a refund.',
            ];
        }

        $method = strtolower((string) ($order->payment_method ?? ''));
        $isCod = str_contains($method, 'cash') || str_contains($method, 'cod');
        $reference = ($isCod ? 'COD-RF-' : 'RF-').strtoupper(Str::random(10));

        return [
            'success' => true,
            'reference' => $reference,
            'message' => sprintf(
                'Refund of $%s recorded against %s.',
                number_format($amountCents / 100, 2),
                $order->payment_method ?? 'original payment method',
            ),
        ];
    }
}
