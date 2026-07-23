<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\AdminCustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminCustomerRepository implements AdminCustomerRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Customer::query()->withCount('orders');

        if ($filters['trashed'] ?? false) {
            $query->onlyTrashed();
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if (($filters['has_orders'] ?? null) === '1') {
            $query->has('orders');
        }

        if (($filters['has_orders'] ?? null) === '0') {
            $query->doesntHave('orders');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function find(int $id): Customer
    {
        return Customer::query()
            ->with([
                'addresses',
                'notes',
                'orders' => fn ($q) => $q->latest('placed_at')->limit(20),
            ])
            ->withCount('orders')
            ->withSum('orders', 'total_cents')
            ->findOrFail($id);
    }

    public function create(array $attributes): Customer
    {
        return Customer::query()->create($attributes);
    }

    public function update(Customer $customer, array $attributes): Customer
    {
        $customer->update($attributes);

        return $customer->fresh()->loadCount('orders');
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    public function restore(int $id): Customer
    {
        $customer = Customer::query()->onlyTrashed()->findOrFail($id);
        $customer->restore();

        return $customer->fresh()->loadCount('orders');
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        return Customer::query()
            ->withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('email', $email)
            ->exists();
    }
}
