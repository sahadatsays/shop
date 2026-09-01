<?php

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Exceptions\Cart\InvalidCouponException;
use App\Models\Cart;
use App\Models\Discount;
use App\Support\MoneyFormatter;

class CouponService
{
    public function __construct(
        private CartRepositoryInterface $carts,
    ) {}

    public function findByCode(string $code): ?Discount
    {
        return Discount::query()
            ->where('code', strtoupper(trim($code)))
            ->first();
    }

    /**
     * @throws InvalidCouponException
     */
    public function validateForSubtotal(Discount $discount, int $subtotalCents): void
    {
        if (! $discount->isAvailable()) {
            throw new InvalidCouponException('This coupon is no longer available.');
        }

        if ($discount->min_order_cents !== null && $subtotalCents < $discount->min_order_cents) {
            throw new InvalidCouponException(
                'Order must be at least '.MoneyFormatter::format($discount->min_order_cents).' to use this coupon.',
            );
        }

        if ($discount->discountAmountCents($subtotalCents) <= 0) {
            throw new InvalidCouponException('This coupon does not apply to your order.');
        }
    }

    /**
     * @throws InvalidCouponException
     */
    public function apply(Cart $cart, string $code, int $subtotalCents): Discount
    {
        $discount = $this->findByCode($code);

        if (! $discount instanceof Discount) {
            throw new InvalidCouponException('That coupon code is not valid.');
        }

        $this->validateForSubtotal($discount, $subtotalCents);

        $this->carts->applyDiscount($cart, $discount);

        return $discount;
    }

    public function remove(Cart $cart): void
    {
        $this->carts->removeDiscount($cart);
    }

    public function resolveApplied(Cart $cart, int $subtotalCents): ?Discount
    {
        $cart->loadMissing('discount');

        $discount = $cart->discount;

        if (! $discount instanceof Discount) {
            return null;
        }

        try {
            $this->validateForSubtotal($discount, $subtotalCents);
        } catch (InvalidCouponException) {
            $this->carts->removeDiscount($cart);
            $cart->unsetRelation('discount');

            return null;
        }

        return $discount;
    }

    /**
     * @throws InvalidCouponException
     */
    public function redeemForOrder(Discount $discount, int $subtotalCents): int
    {
        $this->validateForSubtotal($discount, $subtotalCents);

        $discount->increment('used_count');

        return $discount->discountAmountCents($subtotalCents);
    }
}
