<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CustomerStatus;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ProductStatus;
use App\Enums\RefundReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdvanceOrderStatusRequest;
use App\Http\Requests\Admin\StoreAdminOrderRequest;
use App\Http\Requests\Admin\StoreOrderNoteRequest;
use App\Http\Requests\Admin\StoreOrderPaymentRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\AdminOrderCreateService;
use App\Services\Admin\CustomerService;
use App\Services\Admin\InvoiceService;
use App\Services\Admin\OrderPaymentService;
use App\Services\Admin\OrderService;
use App\Support\MoneyFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private AdminOrderCreateService $orderCreate,
        private OrderPaymentService $orderPayments,
        private InvoiceService $invoices,
        private CustomerService $customers,
    ) {}

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

    public function create(): View
    {
        return view('admin.orders.create', [
            'title' => 'Create order',
            'breadcrumbs' => [
                ['label' => 'Orders', 'href' => route('admin.orders.index')],
                ['label' => 'Create'],
            ],
            'sources' => OrderSource::cases(),
            'paymentMethods' => PaymentMethod::cases(),
            'shippingMethods' => config('cart.shipping_methods', []),
            'taxRate' => (float) config('cart.tax_rate', 0.08),
            'currencySymbol' => MoneyFormatter::symbol(),
            'idempotencyKey' => (string) str()->uuid(),
        ]);
    }

    public function store(StoreAdminOrderRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();

        $payload = $request->validated();

        if (($payload['customer_mode'] ?? null) === 'new') {
            $customer = $this->customers->create([
                'name' => $payload['new_customer']['name'],
                'email' => $payload['new_customer']['email'],
                'phone' => $payload['new_customer']['phone'] ?? null,
                'status' => CustomerStatus::Active->value,
            ]);
            $payload['customer_id'] = $customer->id;
        }

        $payload['initial_payment_cents'] = (int) ($payload['initial_payment_cents'] ?? 0);
        $payload['shipping_cents'] = (int) $payload['shipping_cents'];

        $order = $this->orderCreate->create($payload, $admin);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order '.$order->order_number.' created successfully.');
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());

        $customers = Customer::query()
            ->with('addresses')
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($builder) use ($term): void {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn (Customer $customer): array => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'addresses' => $customer->addresses->map(fn ($address): array => [
                    'id' => $address->id,
                    'label' => $address->label,
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'line1' => $address->line1,
                    'line2' => $address->line2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'is_default' => $address->is_default,
                ])->values()->all(),
            ]);

        return response()->json(['data' => $customers]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());

        $products = Product::query()
            ->with(['category', 'images'])
            ->where('status', ProductStatus::Published)
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($builder) use ($term): void {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('barcode', 'like', "%{$term}%")
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$term}%"));
                });
            })
            ->ordered()
            ->limit(20)
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'category' => $product->category?->name,
                'price_cents' => $product->price_cents,
                'price' => MoneyFormatter::format($product->price_cents),
                'stock_quantity' => $product->stock_quantity,
                'image' => $product->primaryImageUrl(),
            ]);

        return response()->json(['data' => $products]);
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
            'paymentMethods' => PaymentMethod::cases(),
            'refundReasons' => RefundReason::cases(),
            'canRefund' => $order->payment_status->isRefundable() && $order->refundableCents() > 0,
            'refundableAmount' => MoneyFormatter::format($order->refundableCents()),
            'refundableAmountValue' => number_format($order->refundableCents() / 100, 2, '.', ''),
        ]);
    }

    public function storePayment(StoreOrderPaymentRequest $request, Order $order): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();

        $this->orderPayments->record($order, $request->paymentData(), $admin);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Payment recorded successfully.');
    }

    public function invoice(Order $order): View
    {
        $order = $this->orders->show($order->id);
        $invoice = $order->invoice ?? $this->invoices->generateForOrder($order);

        return view('admin.orders.invoice', [
            'title' => $invoice->invoice_number,
            'order' => $order,
            'invoice' => $invoice,
            'snapshot' => $invoice->snapshot,
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

    public function advanceStatus(AdvanceOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $updated = $this->orders->advanceToNextStatus($order);

        return back()->with(
            'success',
            'Order advanced to '.$updated->status->label().'.',
        );
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
