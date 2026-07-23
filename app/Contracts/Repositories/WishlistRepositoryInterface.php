<?php

namespace App\Contracts\Repositories;

use App\Models\Customer;
use App\Models\Wishlist;
use App\Models\WishlistItem;

interface WishlistRepositoryInterface
{
    public function findGuestBySession(string $sessionId): ?Wishlist;

    public function findByCustomer(Customer|int $customer): ?Wishlist;

    public function createGuest(string $sessionId): Wishlist;

    public function createForCustomer(Customer|int $customer): Wishlist;

    public function findOrCreateGuest(string $sessionId): Wishlist;

    public function findOrCreateForCustomer(Customer|int $customer): Wishlist;

    public function loadWithItems(Wishlist $wishlist): Wishlist;

    public function addItem(Wishlist $wishlist, int $productId): WishlistItem;

    public function removeItem(WishlistItem $item): void;

    public function clear(Wishlist $wishlist): void;

    public function delete(Wishlist $wishlist): void;
}
