<?php

namespace App\Services\Admin;

use App\Enums\AuditAction;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimelineEvent;
use App\Models\Product;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationService;
use App\Support\Checkout\OrderTotalsCalculator;
use App\Support\OrderNumberGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class AdminOrderCreateService
{
    public function __construct(
        private InventoryService $inventory,
        private OrderPaymentService $payments,
        private InvoiceService $invoices,
        private OrderTotalsCalculator $totals,
        private NotificationService $notifications,
        private AuditService $audit,
    ) {}

    /**
     * @param  array{
     *     customer_id: int,
     *     source?: string,
     *     shipping_method?: ?string,
     *     shipping_cents: int,
     *     order_discount_type?: ?string,
     *     order_discount_value?: int|float|null,
     *     payment_method: string,
     *     initial_payment_cents?: int,
     *     initial_payment_status?: string,
     *     transaction_reference?: ?string,
     *     shipping_address: array<string, mixed>,
     *     admin_notes?: ?string,
     *     idempotency_key: string,
     *     items: list<array{product_id: int, quantity: int, unit_price_cents?: int|null, discount_cents?: int}>
     * }  $data
     */
    public function create(array $data, User $admin): Order
    {
        $existing = Order::query()->where('idempotency_key', $data['idempotency_key'])->first();

        if ($existing !== null) {
            return $this->ordersShow($existing->id);
        }

        $customer = Customer::query()->findOrFail($data['customer_id']);

        if ($customer->trashed()) {
            throw ValidationException::withMessages([
                'customer_id' => 'This customer has been deleted.',
            ]);
        }

        $lines = $this->normalizeLines($data['items']);

        if ($lines === []) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one product to the order.',
            ]);
        }

        $grossSubtotal = collect($lines)->sum(fn (array $line): int => $line['unit_price_cents'] * $line['quantity']);
        $itemDiscountTotal = collect($lines)->sum(fn (array $line): int => $line['discount_cents']);
        $netSubtotal = max(0, $grossSubtotal - $itemDiscountTotal);
        $orderDiscountCents = $this->resolveOrderDiscountCents(
            $netSubtotal,
            $data['order_discount_type'] ?? null,
            $data['order_discount_value'] ?? null,
        );

        $totals = $this->totals->calculateFromAmounts(
            $netSubtotal,
            $orderDiscountCents,
            max(0, (int) $data['shipping_cents']),
        );

        $initialPaymentCents = max(0, (int) ($data['initial_payment_cents'] ?? 0));

        if ($initialPaymentCents > $totals->totalCents) {
            throw ValidationException::withMessages([
                'initial_payment_cents' => 'Paid amount cannot exceed the order total.',
            ]);
        }

        try {
            $order = DB::transaction(function () use ($data, $admin, $customer, $lines, $totals, $initialPaymentCents): Order {
                $productIds = collect($lines)->pluck('product_id')->all();
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($lines as $index => $line) {
                    $product = $products->get($line['product_id']);

                    if ($product === null || $product->status !== ProductStatus::Published) {
                        throw ValidationException::withMessages([
                            "items.{$index}.product_id" => 'One or more products are unavailable.',
                        ]);
                    }

                    if ($line['quantity'] > $product->stock_quantity) {
                        throw ValidationException::withMessages([
                            "items.{$index}.quantity" => "Only {$product->stock_quantity} units available for {$product->name}.",
                        ]);
                    }
                }

                $paymentMethod = PaymentMethod::from($data['payment_method']);

                $order = Order::query()->create([
                    'customer_id' => $customer->id,
                    'created_by' => $admin->id,
                    'order_number' => OrderNumberGenerator::generate(),
                    'source' => OrderSource::tryFrom((string) ($data['source'] ?? OrderSource::Admin->value)) ?? OrderSource::Admin,
                    'status' => OrderStatus::Pending,
                    'payment_status' => PaymentStatus::Pending,
                    'payment_method' => $paymentMethod->label(),
                    'subtotal_cents' => $totals->subtotalCents,
                    'discount_cents' => $totals->discountCents,
                    'shipping_cents' => $totals->shippingCents,
                    'shipping_method' => $data['shipping_method'] ?? null,
                    'tax_cents' => $totals->taxCents,
                    'total_cents' => $totals->totalCents,
                    'paid_cents' => 0,
                    'shipping_address' => $data['shipping_address'],
                    'admin_notes' => $data['admin_notes'] ?? null,
                    'idempotency_key' => $data['idempotency_key'],
                    'placed_at' => now(),
                ]);

                foreach ($lines as $line) {
                    /** @var Product $product */
                    $product = $products->get($line['product_id']);
                    $lineTotal = ($line['unit_price_cents'] * $line['quantity']) - $line['discount_cents'];

                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $line['quantity'],
                        'unit_price_cents' => $line['unit_price_cents'],
                        'discount_cents' => $line['discount_cents'],
                        'line_total_cents' => max(0, $lineTotal),
                    ]);

                    $this->inventory->deductForSale(
                        $product,
                        $line['quantity'],
                        $order->order_number,
                        'Admin order '.$order->order_number,
                    );
                }

                OrderTimelineEvent::query()->create([
                    'order_id' => $order->id,
                    'status' => OrderStatus::Pending->value,
                    'message' => 'Order created by admin '.$admin->name.'.',
                    'author_name' => $admin->name,
                    'changed_by' => $admin->id,
                ]);

                if ($initialPaymentCents > 0) {
                    $this->payments->record(
                        $order,
                        [
                            'amount_cents' => $initialPaymentCents,
                            'method' => $paymentMethod->value,
                            'status' => PaymentStatus::Paid->value,
                            'transaction_reference' => $data['transaction_reference'] ?? null,
                            'notes' => 'Initial payment recorded with order creation.',
                        ],
                        $admin,
                        syncInvoice: false,
                    );
                }

                $order = $this->ordersShow($order->id);
                $this->invoices->generateForOrder($order);

                return $this->ordersShow($order->id);
            });
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'items' => $exception->getMessage(),
            ]);
        }

        $this->audit->log(
            AuditAction::OrderCreated,
            "Admin created order {$order->order_number}.",
            $order,
            $admin,
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_cents' => $order->total_cents,
                'source' => $order->source instanceof OrderSource ? $order->source->value : OrderSource::Admin->value,
            ],
        );

        $this->notifications->notifyOrderPlaced($order);

        return $order;
    }

    /**
     * @param  list<array{product_id: int, quantity: int, unit_price_cents?: int|null, discount_cents?: int}>  $items
     * @return list<array{product_id: int, quantity: int, unit_price_cents: int, discount_cents: int}>
     */
    private function normalizeLines(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $product = Product::query()->find($productId);

            if ($product === null) {
                continue;
            }

            $unitPrice = array_key_exists('unit_price_cents', $item) && $item['unit_price_cents'] !== null
                ? max(0, (int) $item['unit_price_cents'])
                : (int) $product->price_cents;

            $lineGross = $unitPrice * $quantity;
            $discount = max(0, min((int) ($item['discount_cents'] ?? 0), $lineGross));

            if (isset($normalized[$productId])) {
                $normalized[$productId]['quantity'] += $quantity;
                $normalized[$productId]['discount_cents'] += $discount;
            } else {
                $normalized[$productId] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPrice,
                    'discount_cents' => $discount,
                ];
            }

            $mergedGross = $normalized[$productId]['unit_price_cents'] * $normalized[$productId]['quantity'];
            $normalized[$productId]['discount_cents'] = min($normalized[$productId]['discount_cents'], $mergedGross);
        }

        return array_values($normalized);
    }

    private function resolveOrderDiscountCents(int $subtotalCents, mixed $type, mixed $value): int
    {
        if ($type === null || $value === null || $value === '') {
            return 0;
        }

        $amount = (float) $value;

        if ($amount <= 0) {
            return 0;
        }

        $cents = match ((string) $type) {
            'percent' => (int) round($subtotalCents * (min($amount, 100) / 100)),
            'fixed' => (int) round($amount * 100),
            default => 0,
        };

        return max(0, min($cents, $subtotalCents));
    }

    private function ordersShow(int $id): Order
    {
        return Order::query()
            ->with(['customer', 'items.product', 'payments', 'invoice', 'timelineEvents'])
            ->findOrFail($id);
    }
}
