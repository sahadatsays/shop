<?php

namespace App\Contracts\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Discount;

interface CartRepositoryInterface
{
    public function findGuestBySession(string $sessionId): ?Cart;

    public function findByCustomer(Customer|int $customer): ?Cart;

    public function createGuest(string $sessionId): Cart;

    public function createForCustomer(Customer|int $customer): Cart;

    public function findOrCreateGuest(string $sessionId): Cart;

    public function findOrCreateForCustomer(Customer|int $customer): Cart;

    public function loadWithItems(Cart $cart): Cart;

    public function addItem(Cart $cart, int $productId, int $quantity, int $unitPriceCents): CartItem;

    public function updateItemQuantity(CartItem $item, int $quantity): CartItem;

    public function removeItem(CartItem $item): void;

    public function clearItems(Cart $cart): void;

    public function markSaved(Cart $cart): Cart;

    public function applyDiscount(Cart $cart, Discount $discount): Cart;

    public function removeDiscount(Cart $cart): Cart;

    public function delete(Cart $cart): void;
}
