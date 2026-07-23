<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;

class CartRepository implements CartRepositoryInterface
{
    public function findGuestBySession(string $sessionId): ?Cart
    {
        return Cart::query()
            ->where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->first();
    }

    public function findByCustomer(Customer|int $customer): ?Cart
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return Cart::query()
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createGuest(string $sessionId): Cart
    {
        return Cart::query()->create([
            'session_id' => $sessionId,
            'expires_at' => now()->addDays((int) config('cart.guest_expiry_days', 30)),
        ]);
    }

    public function createForCustomer(Customer|int $customer): Cart
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return Cart::query()->create([
            'customer_id' => $customerId,
        ]);
    }

    public function findOrCreateGuest(string $sessionId): Cart
    {
        return $this->findGuestBySession($sessionId) ?? $this->createGuest($sessionId);
    }

    public function findOrCreateForCustomer(Customer|int $customer): Cart
    {
        return $this->findByCustomer($customer) ?? $this->createForCustomer($customer);
    }

    public function loadWithItems(Cart $cart): Cart
    {
        return $cart->load([
            'items.product.images',
            'items.product.category',
        ]);
    }

    public function addItem(Cart $cart, int $productId, int $quantity, int $unitPriceCents): CartItem
    {
        $existing = $cart->items()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $quantity,
                'unit_price_cents' => $unitPriceCents,
            ]);

            return $existing->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price_cents' => $unitPriceCents,
        ]);
    }

    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function markSaved(Cart $cart): Cart
    {
        $cart->update([
            'is_saved' => true,
            'saved_at' => now(),
        ]);

        return $cart->fresh();
    }

    public function delete(Cart $cart): void
    {
        $cart->delete();
    }
}
