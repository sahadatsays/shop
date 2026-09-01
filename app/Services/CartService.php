<?php

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\DTOs\Cart\CartLineItem;
use App\DTOs\Cart\CartSummary;
use App\Enums\ProductStatus;
use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use App\Support\Checkout\OrderTotalsCalculator;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $carts,
        private CouponService $coupons,
        private OrderTotalsCalculator $totals,
    ) {}

    public function resolve(): Cart
    {
        $customerId = session('customer_id');

        if ($customerId) {
            return $this->resolveCustomerCart((int) $customerId);
        }

        if ($cart = $this->resolveGuestFromSession()) {
            return $this->carts->loadWithItems($cart);
        }

        return $this->resolveGuestCart();
    }

    private function resolveGuestFromSession(): ?Cart
    {
        $cartId = session('cart_id');

        if (! $cartId) {
            return null;
        }

        $cart = Cart::query()->find($cartId);

        if (! $cart || $cart->customer_id !== null) {
            session()->forget('cart_id');

            return null;
        }

        return $cart;
    }

    public function resolveGuestCart(): Cart
    {
        $sessionId = session()->getId();
        $cart = $this->carts->findOrCreateGuest($sessionId);

        session(['cart_id' => $cart->id]);

        return $this->carts->loadWithItems($cart);
    }

    public function resolveCustomerCart(int $customerId): Cart
    {
        $guestCart = $this->resolveGuestFromSession()
            ?? $this->carts->findGuestBySession(session()->getId());

        $customerCart = $this->carts->findOrCreateForCustomer($customerId);

        if ($guestCart && $guestCart->isGuest() && $guestCart->id !== $customerCart->id) {
            $this->mergeCarts($guestCart, $customerCart);
            $customerCart = $customerCart->fresh();
        }

        session(['cart_id' => $customerCart->id]);

        return $this->carts->loadWithItems($customerCart);
    }

    public function mergeCarts(Cart $source, Cart $target): Cart
    {
        return DB::transaction(function () use ($source, $target): Cart {
            $source->load('items.product');

            foreach ($source->items as $item) {
                $product = $item->product;

                if (! $product || $product->trashed() || $product->status !== ProductStatus::Published) {
                    continue;
                }

                $available = min($item->quantity, $product->stock_quantity);

                if ($available <= 0) {
                    continue;
                }

                $this->carts->addItem(
                    $target,
                    $product->id,
                    $available,
                    $product->price_cents,
                );
            }

            if ($source->discount_id && ! $target->discount_id) {
                $target->update(['discount_id' => $source->discount_id]);
            }

            $this->carts->delete($source);

            return $this->carts->loadWithItems($target);
        });
    }

    public function mergeGuestIntoCustomer(Customer|int $customer): Cart
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return $this->resolveCustomerCart($customerId);
    }

    public function addItem(int $productId, int $quantity = 1): CartSummary
    {
        $product = $this->findPurchasableProduct($productId);
        $cart = $this->resolve();

        $existingQuantity = $cart->items
            ->firstWhere('product_id', $productId)?->quantity ?? 0;

        $this->assertStockAvailable($product, $existingQuantity + $quantity);

        $this->carts->addItem($cart, $product->id, $quantity, $product->price_cents);

        return $this->summary($this->carts->loadWithItems($cart->fresh()));
    }

    public function updateQuantity(CartItem $item, int $quantity): CartSummary
    {
        $this->assertCartItemOwnership($item);

        if ($quantity <= 0) {
            return $this->removeItem($item);
        }

        $product = $this->findPurchasableProduct($item->product_id);
        $this->assertStockAvailable($product, $quantity);

        $this->carts->updateItemQuantity($item, $quantity);

        return $this->summary($this->carts->loadWithItems($item->cart->fresh()));
    }

    public function removeItem(CartItem $item): CartSummary
    {
        $this->assertCartItemOwnership($item);
        $cart = $item->cart;

        $this->carts->removeItem($item);

        return $this->summary($this->carts->loadWithItems($cart->fresh()));
    }

    public function saveCart(): CartSummary
    {
        $cart = $this->resolve();

        if ($cart->isGuest()) {
            throw new CartValidationException('Sign in to save your cart for later.');
        }

        $this->validateCart($cart);
        $this->validateStock($cart);

        $this->carts->markSaved($cart);

        return $this->summary($this->carts->loadWithItems($cart->fresh()));
    }

    /**
     * @return array<int, string>
     */
    public function validateCart(?Cart $cart = null): array
    {
        $cart ??= $this->resolve();
        $errors = [];

        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product || $product->trashed()) {
                $errors[] = 'An item in your cart is no longer available.';
                $this->carts->removeItem($item);

                continue;
            }

            if ($product->status !== ProductStatus::Published) {
                $errors[] = "{$product->name} is no longer available for purchase.";
                $this->carts->removeItem($item);

                continue;
            }

            if ($item->unit_price_cents !== $product->price_cents) {
                $item->update(['unit_price_cents' => $product->price_cents]);
            }
        }

        if ($errors !== []) {
            throw new CartValidationException(
                'Some items in your cart were updated or removed.',
                $errors,
            );
        }

        return [];
    }

    /**
     * @return array<int, string>
     */
    public function validateStock(?Cart $cart = null): array
    {
        $cart ??= $this->resolve();
        $errors = [];

        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product) {
                continue;
            }

            if ($item->quantity > $product->stock_quantity) {
                throw new InsufficientStockException(
                    $product,
                    $product->stock_quantity,
                    $item->quantity,
                );
            }
        }

        return $errors;
    }

    public function applyCoupon(string $code): CartSummary
    {
        $cart = $this->resolve();
        $subtotalCents = $this->subtotalCentsForCart($cart);

        $this->coupons->apply($cart, $code, $subtotalCents);

        return $this->summary($this->carts->loadWithItems($cart->fresh()));
    }

    public function removeCoupon(): CartSummary
    {
        $cart = $this->resolve();
        $this->coupons->remove($cart);

        return $this->summary($this->carts->loadWithItems($cart->fresh()));
    }

    public function summary(?Cart $cart = null): CartSummary
    {
        $cart ??= $this->resolve();
        $cart = $this->carts->loadWithItems($cart);

        $items = $cart->items
            ->filter(fn (CartItem $item): bool => $item->product !== null)
            ->map(fn (CartItem $item): CartLineItem => new CartLineItem($item, $item->product));

        $subtotalCents = $items->sum(fn (CartLineItem $line): int => $line->lineTotalCents());
        $itemCount = $items->sum(fn (CartLineItem $line): int => $line->cartItem->quantity);

        $discount = $this->coupons->resolveApplied($cart, $subtotalCents);

        $freeShippingThreshold = (int) config('cart.free_shipping_threshold_cents', 7500);
        $flatShipping = (int) config('cart.flat_shipping_cents', 900);

        $shippingCents = $subtotalCents === 0 || $subtotalCents >= $freeShippingThreshold
            ? 0
            : $flatShipping;

        $totals = $this->totals->calculate($subtotalCents, $shippingCents, $discount);

        return new CartSummary(
            cart: $cart,
            items: $items,
            itemCount: $itemCount,
            subtotalCents: $totals->subtotalCents,
            discountCents: $totals->discountCents,
            shippingCents: $totals->shippingCents,
            taxCents: $totals->taxCents,
            totalCents: $totals->totalCents,
            discount: $totals->discount,
        );
    }

    private function subtotalCentsForCart(Cart $cart): int
    {
        $cart = $this->carts->loadWithItems($cart);

        return (int) $cart->items
            ->filter(fn (CartItem $item): bool => $item->product !== null)
            ->sum(fn (CartItem $item): int => $item->quantity * $item->unit_price_cents);
    }

    public function itemCount(): int
    {
        return $this->summary()->itemCount;
    }

    private function findPurchasableProduct(int $productId): Product
    {
        $product = Product::query()->find($productId);

        if (! $product || $product->trashed()) {
            throw new CartValidationException('This product is no longer available.');
        }

        if ($product->status !== ProductStatus::Published) {
            throw new CartValidationException('This product is not available for purchase.');
        }

        return $product;
    }

    private function assertStockAvailable(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock_quantity) {
            throw new InsufficientStockException(
                $product,
                $product->stock_quantity,
                $quantity,
            );
        }
    }

    private function assertCartItemOwnership(CartItem $item): void
    {
        $cart = $this->resolve();

        if ($item->cart_id !== $cart->id) {
            abort(403);
        }
    }
}
