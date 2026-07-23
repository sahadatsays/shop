<?php

namespace App\Contracts\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminCustomerRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Customer;

    public function create(array $attributes): Customer;

    public function update(Customer $customer, array $attributes): Customer;

    public function delete(Customer $customer): bool;

    public function restore(int $id): Customer;

    public function emailExists(string $email, ?int $ignoreId = null): bool;
}
