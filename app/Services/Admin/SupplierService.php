<?php

namespace App\Services\Admin;

use App\Enums\PurchaseStatus;
use App\Enums\SupplierStatus;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Support\Suppliers\SupplierPurchaseSummary;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierService
{
    /**
     * @param  array{search?: ?string, status?: ?string}  $filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Supplier::query()->ordered();

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if (($status = $filters['status'] ?? null) !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate(15)->withQueryString();
    }

    public function find(int $id): Supplier
    {
        return Supplier::query()->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Supplier
    {
        return DB::transaction(function () use ($data): Supplier {
            return Supplier::query()->create($this->prepareAttributes($data));
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        return DB::transaction(function () use ($supplier, $data): Supplier {
            $supplier->update($this->prepareAttributes($data));

            return $supplier->fresh();
        });
    }

    public function delete(Supplier $supplier): void
    {
        if ($this->hasPurchaseHistory($supplier)) {
            throw ValidationException::withMessages([
                'supplier' => 'Suppliers with purchase history cannot be deleted. Mark them inactive instead.',
            ]);
        }

        $supplier->delete();
    }

    /**
     * Active suppliers for Purchase Management selection lists.
     *
     * @return Collection<int, Supplier>
     */
    public function selectableForPurchase(): Collection
    {
        return Supplier::query()
            ->selectableForPurchase()
            ->ordered()
            ->get();
    }

    public function assertSelectableForPurchase(Supplier $supplier): void
    {
        if (! $supplier->isSelectableForPurchase()) {
            throw ValidationException::withMessages([
                'supplier_id' => 'Inactive suppliers cannot be selected for new purchases.',
            ]);
        }
    }

    public function purchaseSummary(Supplier $supplier): SupplierPurchaseSummary
    {
        $purchases = Purchase::query()
            ->with('items')
            ->where('supplier_id', $supplier->id)
            ->whereNot('status', PurchaseStatus::Cancelled)
            ->get();

        if ($purchases->isEmpty()) {
            return SupplierPurchaseSummary::empty();
        }

        $products = [];

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $key = (string) $item->product_id;
                $products[$key] ??= [
                    'product_id' => $item->product_id,
                    'name' => $item->product_name_snapshot,
                    'quantity' => 0,
                    'total_cents' => 0,
                ];
                $products[$key]['quantity'] += $item->quantity_ordered;
                $products[$key]['total_cents'] += $item->subtotal_cents;
            }
        }

        $lastPurchase = $purchases
            ->sortByDesc(fn (Purchase $purchase) => $purchase->purchase_date?->timestamp ?? 0)
            ->first();

        return new SupplierPurchaseSummary(
            purchaseCount: $purchases->count(),
            totalPurchaseValueCents: (int) $purchases->sum('grand_total_cents'),
            outstandingPayableCents: (int) $purchases->sum(fn (Purchase $purchase): int => $purchase->dueCents()),
            lastPurchaseAt: $lastPurchase?->purchase_date?->toDateString(),
            productsPurchased: array_values($products),
        );
    }

    public function hasPurchaseHistory(Supplier $supplier): bool
    {
        return Purchase::query()
            ->withTrashed()
            ->where('supplier_id', $supplier->id)
            ->exists();
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function purchaseHistory(Supplier $supplier, int $limit = 20): Collection
    {
        return Purchase::query()
            ->withSum('items as quantity_ordered_sum', 'quantity_ordered')
            ->withSum('items as quantity_received_sum', 'quantity_received')
            ->where('supplier_id', $supplier->id)
            ->latest('purchase_date')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        $status = $data['status'] ?? SupplierStatus::Active->value;

        if ($status instanceof SupplierStatus) {
            $status = $status->value;
        }

        return [
            'name' => $data['name'],
            'company_name' => $data['company_name'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => filled($data['email'] ?? null) ? strtolower((string) $data['email']) : null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'country' => $data['country'] ?? 'Bangladesh',
            'tax_id' => $data['tax_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $status,
        ];
    }
}
