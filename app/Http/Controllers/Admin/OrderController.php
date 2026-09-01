<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\RefundReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderNoteRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\Admin\OrderService;
use App\Support\MoneyFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function index(Request $request): View
    {
        return view('admin.orders.index', [
            'title' => 'Orders',
            'breadcrumbs' => [
                ['label' => 'Orders'],
            ],
            'orders' => $this->orders->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ]),
            'summary' => $this->orders->summary(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Order $order): View
    {
        $order = $this->orders->show($order->id);

        return view('admin.orders.show', [
            'title' => $order->order_number,
            'breadcrumbs' => [
                ['label' => 'Orders', 'href' => route('admin.orders.index')],
                ['label' => $order->order_number],
            ],
            'order' => $order,
            'statuses' => OrderStatus::cases(),
            'refundReasons' => RefundReason::cases(),
            'canRefund' => $order->payment_status->isRefundable() && $order->refundableCents() > 0,
            'refundableAmount' => MoneyFormatter::format($order->refundableCents()),
            'refundableAmountValue' => number_format($order->refundableCents() / 100, 2, '.', ''),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->orders->updateStatus(
            $order,
            $request->enum('status', OrderStatus::class),
            $request->string('message')->toString() ?: null,
            $request->string('author_name')->toString() ?: null,
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated.');
    }

    public function storeNote(StoreOrderNoteRequest $request, Order $order): RedirectResponse
    {
        $this->orders->addNote(
            $order,
            $request->string('body')->toString(),
            $request->string('author_name')->toString() ?: null,
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Note added successfully.');
    }
}
