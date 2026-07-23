<?php

namespace App\Contracts\Repositories;

use App\Models\CompareItem;
use App\Models\CompareList;
use App\Models\Customer;

interface CompareRepositoryInterface
{
    public function findGuestBySession(string $sessionId): ?CompareList;

    public function findByCustomer(Customer|int $customer): ?CompareList;

    public function createGuest(string $sessionId): CompareList;

    public function createForCustomer(Customer|int $customer): CompareList;

    public function findOrCreateGuest(string $sessionId): CompareList;

    public function findOrCreateForCustomer(Customer|int $customer): CompareList;

    public function loadWithItems(CompareList $compareList): CompareList;

    public function addItem(CompareList $compareList, int $productId): CompareItem;

    public function removeItem(CompareItem $item): void;

    public function clear(CompareList $compareList): void;

    public function delete(CompareList $compareList): void;
}
