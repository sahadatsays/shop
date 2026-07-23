<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CompareRepositoryInterface;
use App\Models\CompareItem;
use App\Models\CompareList;
use App\Models\Customer;

class CompareRepository implements CompareRepositoryInterface
{
    public function findGuestBySession(string $sessionId): ?CompareList
    {
        return CompareList::query()
            ->where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->first();
    }

    public function findByCustomer(Customer|int $customer): ?CompareList
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return CompareList::query()
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createGuest(string $sessionId): CompareList
    {
        return CompareList::query()->create([
            'session_id' => $sessionId,
            'expires_at' => now()->addDays((int) config('compare.guest_expiry_days', 90)),
        ]);
    }

    public function createForCustomer(Customer|int $customer): CompareList
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return CompareList::query()->create([
            'customer_id' => $customerId,
        ]);
    }

    public function findOrCreateGuest(string $sessionId): CompareList
    {
        return $this->findGuestBySession($sessionId) ?? $this->createGuest($sessionId);
    }

    public function findOrCreateForCustomer(Customer|int $customer): CompareList
    {
        return $this->findByCustomer($customer) ?? $this->createForCustomer($customer);
    }

    public function loadWithItems(CompareList $compareList): CompareList
    {
        return $compareList->load([
            'items.product.images',
            'items.product.category',
            'items.product.brand',
            'items.product.specifications',
            'items.product.attributes',
        ]);
    }

    public function addItem(CompareList $compareList, int $productId): CompareItem
    {
        return $compareList->items()->firstOrCreate([
            'product_id' => $productId,
        ]);
    }

    public function removeItem(CompareItem $item): void
    {
        $item->delete();
    }

    public function clear(CompareList $compareList): void
    {
        $compareList->items()->delete();
    }

    public function delete(CompareList $compareList): void
    {
        $compareList->delete();
    }
}
