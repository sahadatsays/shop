<?php

namespace App\Services;

use App\Contracts\Repositories\CustomerOrderRepositoryInterface;
use App\Exceptions\OrderTrackingException;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Collection;

class OrderTrackingService
{
    public const SESSION_KEY = 'verified_order_tracking';

    public function __construct(
        private CustomerOrderRepositoryInterface $orders,
    ) {}

    /**
     * @throws OrderTrackingException
     */
    public function lookup(string $orderNumber, string $email): Order
    {
        $order = $this->orders->findByNumberAndEmail($orderNumber, $email);

        if (! $order) {
            throw OrderTrackingException::invalidCredentials();
        }

        $this->rememberVerifiedOrder($order);

        return $this->orders->findTrackable($order);
    }

    public function show(Order $order): Order
    {
        return $this->orders->findTrackable($order);
    }

    /**
     * @throws OrderTrackingException
     */
    public function authorizeView(Order $order, ?Customer $customer = null): void
    {
        if ($customer !== null && (int) $order->customer_id === (int) $customer->id) {
            return;
        }

        if ($this->isVerifiedInSession($order)) {
            return;
        }

        throw OrderTrackingException::unauthorized();
    }

    /**
     * @return Collection<int, Order>
     */
    public function listForCustomer(Customer $customer, array $filters = []): Collection
    {
        return $this->orders->listForCustomer($customer, $filters);
    }

    public function rememberVerifiedOrder(Order $order): void
    {
        $verified = collect(session(self::SESSION_KEY, []))
            ->push($this->verificationToken($order))
            ->unique()
            ->values()
            ->all();

        session([self::SESSION_KEY => $verified]);
    }

    public function isVerifiedInSession(Order $order): bool
    {
        return in_array($this->verificationToken($order), session(self::SESSION_KEY, []), true);
    }

    private function verificationToken(Order $order): string
    {
        return hash('sha256', $order->getKey().'|'.$order->order_number.'|'.$order->customer_id);
    }
}
