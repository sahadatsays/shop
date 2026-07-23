<?php

namespace App\Contracts\Repositories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Collection;

interface CustomerOrderRepositoryInterface
{
    public function findByNumberAndEmail(string $orderNumber, string $email): ?Order;

    public function findForCustomer(Customer $customer, Order $order): Order;

    /**
     * @return Collection<int, Order>
     */
    public function listForCustomer(Customer $customer, array $filters = []): Collection;

    public function findTrackable(Order $order): Order;
}
