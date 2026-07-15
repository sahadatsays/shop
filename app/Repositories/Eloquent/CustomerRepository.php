<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\DTOs\Admin\Dashboard\CustomerSummaryData;
use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function countTotal(): int
    {
        return Customer::query()->count();
    }

    public function latest(int $limit = 5): Collection
    {
        return Customer::query()
            ->withCount('orders')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Customer $customer): CustomerSummaryData => new CustomerSummaryData(
                name: $customer->name,
                email: $customer->email,
                joinedAt: $customer->created_at->diffForHumans(),
                orderCount: $customer->orders_count,
            ));
    }
}
