<?php

namespace App\Services;

use App\Contracts\Repositories\CompareRepositoryInterface;
use App\DTOs\Compare\CompareLineItem;
use App\DTOs\Compare\ComparePageData;
use App\DTOs\Compare\CompareProductColumn;
use App\DTOs\Compare\CompareSummary;
use App\Enums\ProductStatus;
use App\Exceptions\Compare\CompareValidationException;
use App\Models\CompareItem;
use App\Models\CompareList;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CompareService
{
    public function __construct(
        private CompareRepositoryInterface $compareLists,
    ) {}

    public function resolve(): CompareList
    {
        $customerId = session('customer_id');

        if ($customerId) {
            return $this->resolveCustomerCompareList((int) $customerId);
        }

        if ($compareList = $this->resolveGuestFromSession()) {
            return $this->compareLists->loadWithItems($compareList);
        }

        return $this->resolveGuestCompareList();
    }

    private function resolveGuestFromSession(): ?CompareList
    {
        $compareListId = session('compare_list_id');

        if (! $compareListId) {
            return null;
        }

        $compareList = CompareList::query()->find($compareListId);

        if (! $compareList || $compareList->customer_id !== null) {
            session()->forget('compare_list_id');

            return null;
        }

        return $compareList;
    }

    public function resolveGuestCompareList(): CompareList
    {
        $sessionId = session()->getId();
        $compareList = $this->compareLists->findOrCreateGuest($sessionId);

        session(['compare_list_id' => $compareList->id]);

        return $this->compareLists->loadWithItems($compareList);
    }

    public function resolveCustomerCompareList(int $customerId): CompareList
    {
        $guestCompareList = $this->resolveGuestFromSession()
            ?? $this->compareLists->findGuestBySession(session()->getId());

        $customerCompareList = $this->compareLists->findOrCreateForCustomer($customerId);

        if ($guestCompareList && $guestCompareList->isGuest() && $guestCompareList->id !== $customerCompareList->id) {
            $this->mergeCompareLists($guestCompareList, $customerCompareList);
            $customerCompareList = $customerCompareList->fresh();
        }

        session(['compare_list_id' => $customerCompareList->id]);

        return $this->compareLists->loadWithItems($customerCompareList);
    }

    public function mergeGuestIntoCustomer(Customer|int $customer): CompareList
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return $this->resolveCustomerCompareList($customerId);
    }

    public function mergeCompareLists(CompareList $source, CompareList $target): CompareList
    {
        return DB::transaction(function () use ($source, $target): CompareList {
            $source->load('items');
            $maxItems = $this->maxItems();

            foreach ($source->items as $item) {
                if ($target->itemCount() >= $maxItems) {
                    break;
                }

                $this->compareLists->addItem($target, $item->product_id);
            }

            $this->compareLists->delete($source);

            return $this->compareLists->loadWithItems($target);
        });
    }

    public function addItem(int $productId): CompareSummary
    {
        $product = $this->findComparableProduct($productId);
        $compareList = $this->resolve();

        if ($compareList->items->contains('product_id', $product->id)) {
            return $this->summary($this->compareLists->loadWithItems($compareList->fresh()));
        }

        if ($compareList->itemCount() >= $this->maxItems()) {
            throw new CompareValidationException(
                'You can compare up to '.$this->maxItems().' products at a time. Remove one to add another.',
            );
        }

        $this->compareLists->addItem($compareList, $product->id);

        return $this->summary($this->compareLists->loadWithItems($compareList->fresh()));
    }

    public function removeItem(CompareItem $item): CompareSummary
    {
        $this->assertCompareItemOwnership($item);
        $compareList = $item->compareList;

        $this->compareLists->removeItem($item);

        return $this->summary($this->compareLists->loadWithItems($compareList->fresh()));
    }

    /**
     * @return array{in_compare: bool, summary: CompareSummary}
     */
    public function toggle(int $productId): array
    {
        $compareList = $this->resolve();
        $existing = $compareList->items->firstWhere('product_id', $productId);

        if ($existing) {
            $this->compareLists->removeItem($existing);

            return [
                'in_compare' => false,
                'summary' => $this->summary($this->compareLists->loadWithItems($compareList->fresh())),
            ];
        }

        $product = $this->findComparableProduct($productId);

        if ($compareList->itemCount() >= $this->maxItems()) {
            throw new CompareValidationException(
                'You can compare up to '.$this->maxItems().' products at a time. Remove one to add another.',
            );
        }

        $this->compareLists->addItem($compareList, $product->id);

        return [
            'in_compare' => true,
            'summary' => $this->summary($this->compareLists->loadWithItems($compareList->fresh())),
        ];
    }

    public function clear(): CompareSummary
    {
        $compareList = $this->resolve();
        $this->compareLists->clear($compareList);

        return $this->summary($this->compareLists->loadWithItems($compareList->fresh()));
    }

    public function summary(?CompareList $compareList = null): CompareSummary
    {
        $compareList ??= $this->resolve();
        $compareList = $this->compareLists->loadWithItems($compareList);

        $items = $compareList->items
            ->filter(fn (CompareItem $item): bool => $item->product !== null)
            ->map(fn (CompareItem $item): CompareLineItem => new CompareLineItem($item, $item->product));

        return new CompareSummary(
            compareList: $compareList,
            items: $items,
            itemCount: $items->count(),
        );
    }

    public function pageData(): ComparePageData
    {
        $summary = $this->summary();

        $columns = $summary->items->map(
            fn (CompareLineItem $line): CompareProductColumn => CompareProductColumn::fromLineItem($line),
        );

        $specificationLabels = $columns
            ->flatMap(fn (CompareProductColumn $column) => $column->specifications->pluck('name'))
            ->unique(fn (string $name): string => strtolower($name))
            ->values()
            ->all();

        return new ComparePageData(
            columns: $columns,
            specificationLabels: $specificationLabels,
        );
    }

    public function itemCount(): int
    {
        return $this->summary()->itemCount;
    }

    /**
     * @return list<int>
     */
    public function productIds(): array
    {
        return $this->summary()->productIds();
    }

    public function contains(int $productId): bool
    {
        return in_array($productId, $this->productIds(), true);
    }

    public function maxItems(): int
    {
        return (int) config('compare.max_items', 4);
    }

    private function findComparableProduct(int $productId): Product
    {
        $product = Product::query()->find($productId);

        if (! $product || $product->trashed()) {
            throw new CompareValidationException('This product is no longer available.');
        }

        if ($product->status !== ProductStatus::Published) {
            throw new CompareValidationException('This product cannot be added to compare.');
        }

        return $product;
    }

    private function assertCompareItemOwnership(CompareItem $item): void
    {
        $compareList = $this->resolve();

        if ($item->compare_list_id !== $compareList->id) {
            abort(403);
        }
    }
}
