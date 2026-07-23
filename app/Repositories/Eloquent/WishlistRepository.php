<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\WishlistRepositoryInterface;
use App\Models\Customer;
use App\Models\Wishlist;
use App\Models\WishlistItem;

class WishlistRepository implements WishlistRepositoryInterface
{
    public function findGuestBySession(string $sessionId): ?Wishlist
    {
        return Wishlist::query()
            ->where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->first();
    }

    public function findByCustomer(Customer|int $customer): ?Wishlist
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return Wishlist::query()
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createGuest(string $sessionId): Wishlist
    {
        return Wishlist::query()->create([
            'session_id' => $sessionId,
            'expires_at' => now()->addDays((int) config('wishlist.guest_expiry_days', 90)),
        ]);
    }

    public function createForCustomer(Customer|int $customer): Wishlist
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return Wishlist::query()->create([
            'customer_id' => $customerId,
        ]);
    }

    public function findOrCreateGuest(string $sessionId): Wishlist
    {
        return $this->findGuestBySession($sessionId) ?? $this->createGuest($sessionId);
    }

    public function findOrCreateForCustomer(Customer|int $customer): Wishlist
    {
        return $this->findByCustomer($customer) ?? $this->createForCustomer($customer);
    }

    public function loadWithItems(Wishlist $wishlist): Wishlist
    {
        return $wishlist->load([
            'items.product.images',
            'items.product.category',
            'items.product.brand',
        ]);
    }

    public function addItem(Wishlist $wishlist, int $productId): WishlistItem
    {
        return $wishlist->items()->firstOrCreate([
            'product_id' => $productId,
        ]);
    }

    public function removeItem(WishlistItem $item): void
    {
        $item->delete();
    }

    public function clear(Wishlist $wishlist): void
    {
        $wishlist->items()->delete();
    }

    public function delete(Wishlist $wishlist): void
    {
        $wishlist->delete();
    }
}
