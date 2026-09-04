<?php

namespace App\Services\Admin;

use App\Enums\SupplierStatus;
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
        // Purchase Management will populate these aggregates from purchase orders,
        // payments, returns, and ledger balances. Soft-deleted suppliers remain
        // resolvable so historical purchase references stay intact.
        unset($supplier);

        return SupplierPurchaseSummary::empty();
    }

    public function hasPurchaseHistory(Supplier $supplier): bool
    {
        // Reserved for Purchase Management (purchase_orders.supplier_id, etc.).
        unset($supplier);

        return false;
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
