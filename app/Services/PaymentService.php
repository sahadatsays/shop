<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Capture payment for a newly placed order.
     * Mock implementation — replace with Stripe/PayPal API when integrated.
     *
     * @return array{success: bool, reference: ?string, message: ?string}
     */
    public function capture(Order $order): array
    {
        if ($order->payment_status !== PaymentStatus::Pending) {
            return [
                'success' => false,
                'reference' => null,
                'message' => 'This order payment has already been processed.',
            ];
        }

        $reference = 'PM-'.strtoupper(Str::random(12));

        return [
            'success' => true,
            'reference' => $reference,
            'message' => 'Payment captured successfully.',
        ];
    }

    /**
     * Issue a refund to the original payment method.
     * Mock implementation — replace with Stripe/PayPal API when integrated.
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

        $reference = 'RF-'.strtoupper(Str::random(12));

        return [
            'success' => true,
            'reference' => $reference,
            'message' => sprintf(
                'Refund of $%s issued to %s.',
                number_format($amountCents / 100, 2),
                $order->payment_method ?? 'original payment method',
            ),
        ];
    }
}
