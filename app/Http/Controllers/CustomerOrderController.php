<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerOrderSummaryResource;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderTrackingResource;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderTrackingService;
use App\Services\ProductReviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function __construct(
        private OrderTrackingService $tracking,
        private ProductReviewService $productReviews,
    ) {}

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
        $tracking = OrderTrackingResource::make($order)->resolve();
        $tracking['items'] = OrderItemResource::collection($order->items)->resolve();

        $reviewableProductIds = [];

        if ($tracking['is_delivered']) {
            foreach ($order->items as $item) {
                if ($item->product && $this->productReviews->canReview($customer, $item->product)) {
                    $reviewableProductIds[] = $item->product_id;
                }
            }
        }

        return view('account-order-show', [
            'title' => 'Order '.$tracking['order_number_display'],
            'order' => $tracking,
            'reviewableProductIds' => $reviewableProductIds,
        ]);
    }

    private function customer(): Customer
    {
        $customerId = session('customer_id');

        return Customer::query()->findOrFail($customerId);
    }
}
