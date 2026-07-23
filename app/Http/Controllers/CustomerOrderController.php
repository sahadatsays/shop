<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerOrderSummaryResource;
use App\Http\Resources\OrderTrackingResource;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function __construct(private OrderTrackingService $tracking) {}

    public function index(Request $request): View
    {
        $customer = $this->customer();

        $orders = CustomerOrderSummaryResource::collection(
            $this->tracking->listForCustomer($customer, [
                'status_group' => $request->string('status')->toString() ?: null,
            ]),
        )->resolve();

        return view('account-orders', [
            'title' => 'Order History',
            'orders' => $orders,
            'filters' => ['All', 'Processing', 'In transit', 'Delivered'],
            'activeFilter' => $request->string('status')->toString() ?: 'All',
            'ordersCount' => count($orders),
        ]);
    }

    public function show(Order $order): View
    {
        $customer = $this->customer();
        $this->tracking->authorizeView($order, $customer);

        $order = $this->tracking->show($order);

        return view('track', [
            'title' => 'Track Shipment',
            'tracking' => OrderTrackingResource::make($order)->resolve(),
            'backUrl' => route('account.orders'),
        ]);
    }

    private function customer(): Customer
    {
        $customerId = session('customer_id');

        return Customer::query()->findOrFail($customerId);
    }
}
