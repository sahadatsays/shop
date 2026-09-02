<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerReturnRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderTrackingService;
use App\Services\RefundService;
use Illuminate\Http\RedirectResponse;

class CustomerReturnController extends Controller
{
    public function __construct(
        private RefundService $refunds,
        private OrderTrackingService $tracking,
    ) {}

    public function store(CustomerReturnRequest $request, Order $order): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $this->tracking->authorizeView($order, $customer);

        $this->refunds->requestReturn(
            $order,
            $customer,
            $request->string('reason')->toString(),
        );

        return redirect()
            ->route('account.orders.show', $order)
            ->with('success', 'Your return request was submitted. We will email you a prepaid return label within 1 business day.');
    }
}
