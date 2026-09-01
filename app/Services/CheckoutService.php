<?php

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\CustomerAuthRepositoryInterface;
use App\DTOs\Cart\CartSummary;
use App\Enums\CustomerStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Exceptions\Cart\InvalidCouponException;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimelineEvent;
use App\Models\Product;
use App\Support\Checkout\OrderTotalsCalculator;
use App\Support\MoneyFormatter;
use App\Support\OrderNumberGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private CartService $cart,
        private CartRepositoryInterface $carts,
        private CouponService $coupons,
        private CustomerAuthRepositoryInterface $customers,
        private NotificationService $notifications,
        private OrderTotalsCalculator $totals,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function pageData(CartSummary $summary): array
    {
        $methods = collect(config('cart.shipping_methods', []))
            ->map(function (array $method, string $key) use ($summary): array {
                $costCents = $this->resolveShippingCostCents($key, $summary->subtotalCents);

                return [
                    'value' => $key,
                    'label' => $method['label'],
                    'description' => $method['description'],
                    'cost_cents' => $costCents,
                    'price' => $costCents === 0 ? 'Free' : MoneyFormatter::format($costCents),
                ];
            })
            ->values()
            ->all();

        return [
            'shippingMethods' => $methods,
            'taxRate' => (float) config('cart.tax_rate', 0.08),
            'currencySymbol' => MoneyFormatter::symbol(),
        ];
    }

    public function placeOrder(PlaceOrderRequest $request): Order
    {
        $this->cart->validateCart();
        $this->cart->validateStock();

        $summary = $this->cart->summary();

        if ($summary->isEmpty()) {
            throw new CartValidationException('Your cart is empty. Add items before checking out.');
        }

        $shippingCents = $this->resolveShippingCostCents(
            $request->validated('delivery_method'),
            $summary->subtotalCents,
        );

        $discount = $summary->discount;
        $totals = $this->totals->calculate($summary->subtotalCents, $shippingCents, $discount);

        return DB::transaction(function () use ($request, $summary, $totals, $discount): Order {
            $customer = $this->resolveCustomer($request);
            $cart = $summary->cart;

            $this->lockAndValidateStock($cart);

            $appliedDiscount = $this->resolveOrderDiscount($discount, $summary->subtotalCents);

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'order_number' => OrderNumberGenerator::generate(),
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Paid,
                'payment_method' => $this->paymentMethodLabel($request->validated('payment_method')),
                'subtotal_cents' => $totals->subtotalCents,
                'discount_cents' => $totals->discountCents,
                'discount_id' => $appliedDiscount?->id,
                'coupon_code' => $appliedDiscount?->code,
                'shipping_cents' => $totals->shippingCents,
                'tax_cents' => $totals->taxCents,
                'total_cents' => $totals->totalCents,
                'shipping_address' => $request->shippingAddress(),
                'billing_address' => $request->billingAddress(),
                'placed_at' => now(),
            ]);

            foreach ($summary->items as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $line->product->id,
                    'quantity' => $line->cartItem->quantity,
                    'unit_price_cents' => $line->cartItem->unit_price_cents,
                    'line_total_cents' => $line->lineTotalCents(),
                ]);

                Product::query()
                    ->whereKey($line->product->id)
                    ->decrement('stock_quantity', $line->cartItem->quantity);
            }

            OrderTimelineEvent::query()->create([
                'order_id' => $order->id,
                'status' => OrderStatus::Pending->value,
                'message' => 'Order placed.',
                'author_name' => $customer->name,
                'created_at' => $order->placed_at,
                'updated_at' => $order->placed_at,
            ]);

            $this->notifications->notifyOrderPlaced($order->load(['customer', 'items']));

            $this->carts->clearItems($cart);

            if ($customer->id !== session('customer_id')) {
                session(['customer_id' => $customer->id]);
            }

            session(['checkout_order_id' => $order->id]);

            return $order->load(['items.product', 'customer']);
        });
    }

    /**
     * @throws InvalidCouponException
     */
    private function resolveOrderDiscount(?Discount $discount, int $subtotalCents): ?Discount
    {
        if (! $discount instanceof Discount) {
            return null;
        }

        $this->coupons->redeemForOrder($discount, $subtotalCents);

        return $discount->fresh();
    }

    public function resolveShippingCostCents(string $method, int $subtotalCents): int
    {
        $config = config("cart.shipping_methods.{$method}");

        if (! is_array($config)) {
            return (int) config('cart.flat_shipping_cents', 900);
        }

        if ($config['cost_cents'] !== null) {
            return (int) $config['cost_cents'];
        }

        $threshold = (int) config('cart.free_shipping_threshold_cents', 7500);

        if ($subtotalCents === 0 || $subtotalCents >= $threshold) {
            return 0;
        }

        return (int) config('cart.flat_shipping_cents', 900);
    }

    private function resolveCustomer(PlaceOrderRequest $request): Customer
    {
        $sessionCustomerId = session('customer_id');

        if ($sessionCustomerId) {
            $customer = Customer::query()->find($sessionCustomerId);

            if ($customer instanceof Customer) {
                $updates = [];

                if ($customer->email !== Str::lower($request->validated('email'))) {
                    $updates['email'] = Str::lower($request->validated('email'));
                }

                $phone = $request->validated('shipping.phone');

                if ($phone && $customer->phone !== $phone) {
                    $updates['phone'] = $phone;
                }

                if ($updates !== []) {
                    $customer = $this->customers->update($customer, $updates);
                }

                return $customer;
            }
        }

        $email = Str::lower($request->validated('email'));
        $existing = $this->customers->findByEmail($email);

        if ($existing instanceof Customer) {
            return $existing;
        }

        $shipping = $request->validated('shipping');

        return $this->customers->create([
            'name' => trim("{$shipping['first_name']} {$shipping['last_name']}"),
            'email' => $email,
            'phone' => $shipping['phone'] ?? null,
            'password' => null,
            'status' => CustomerStatus::Active,
        ]);
    }

    private function lockAndValidateStock(Cart $cart): void
    {
        $cart = $this->carts->loadWithItems($cart);

        foreach ($cart->items as $item) {
            $product = Product::query()->lockForUpdate()->find($item->product_id);

            if (! $product) {
                throw new CartValidationException('An item in your cart is no longer available.');
            }

            if ($item->quantity > $product->stock_quantity) {
                throw new InsufficientStockException(
                    $product,
                    $product->stock_quantity,
                    $item->quantity,
                );
            }
        }
    }

    private function paymentMethodLabel(string $method): string
    {
        return match ($method) {
            'paypal' => 'PayPal',
            'applepay' => 'Apple Pay',
            default => 'Card',
        };
    }
}
