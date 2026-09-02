<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\RefundReason;
use App\Enums\RefundStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use App\Services\Admin\InventoryService;
use App\Services\Admin\OrderService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundService
{
    public function __construct(
        private PaymentService $payments,
        private OrderService $orders,
        private InventoryService $inventory,
        private NotificationService $notifications,
        private AuditService $audit,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Refund::query()
            ->with(['order.customer', 'processedBy'])
            ->latest();

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('payment_reference', 'like', $term)
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery
                        ->where('order_number', 'like', $term)
                        ->orWhereHas('customer', fn ($customerQuery) => $customerQuery
                            ->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)));
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($filter = $filters['filter'] ?? null) {
            match ($filter) {
                'pending_returns' => $query->whereHas('order', fn ($orderQuery) => $orderQuery->needsRefundAttention()),
                'completed' => $query->where('status', RefundStatus::Completed->value),
                default => null,
            };
        }

        return $query->paginate(15)->withQueryString();
    }

    public function find(int $id): Refund
    {
        return Refund::query()
            ->with(['order.customer', 'order.items.product', 'order.refunds', 'processedBy'])
            ->findOrFail($id);
    }

    /**
     * @return array{pending_returns: int, completed_today: int, refunded_cents: int}
     */
    public function summary(): array
    {
        return [
            'pending_returns' => Order::query()->needsRefundAttention()->count(),
            'completed_today' => Refund::query()
                ->where('status', RefundStatus::Completed)
                ->whereDate('processed_at', today())
                ->count(),
            'refunded_cents' => (int) Refund::query()
                ->where('status', RefundStatus::Completed)
                ->sum('amount_cents'),
        ];
    }

    public function requestReturn(Order $order, Customer $customer, string $reason): Order
    {
        if ((int) $order->customer_id !== (int) $customer->id) {
            throw ValidationException::withMessages([
                'order' => 'You are not authorized to request a return for this order.',
            ]);
        }

        if (! $order->canRequestReturn()) {
            throw ValidationException::withMessages([
                'reason' => 'This order is not eligible for a return request.',
            ]);
        }

        return DB::transaction(function () use ($order, $reason): Order {
            $order->update([
                'return_requested_at' => now(),
                'return_reason' => $reason,
            ]);

            $this->orders->updateStatus(
                $order->fresh(),
                OrderStatus::Returned,
                'Customer requested a return. A prepaid label will be emailed within 1 business day.',
                $order->customer?->name ?? 'Customer',
            );

            $this->notifyAdminsOfReturnRequest($order->fresh(['customer']));

            return $order->fresh(['customer', 'items.product', 'timelineEvents', 'refunds']);
        });
    }

    public function processRefund(
        Order $order,
        int $amountCents,
        RefundReason $reason,
        ?string $notes,
        bool $restoreStock,
        ?User $admin = null,
    ): Refund {
        if ($amountCents <= 0) {
            throw ValidationException::withMessages([
                'amount_cents' => 'Refund amount must be greater than zero.',
            ]);
        }

        if ($amountCents > $order->refundableCents()) {
            throw ValidationException::withMessages([
                'amount_cents' => 'Refund amount exceeds the remaining refundable balance.',
            ]);
        }

        $paymentStatus = $order->payment_status instanceof PaymentStatus
            ? $order->payment_status
            : PaymentStatus::tryFrom((string) $order->payment_status) ?? PaymentStatus::Paid;

        if (! $paymentStatus->isRefundable()) {
            throw ValidationException::withMessages([
                'order' => 'This order has already been fully refunded.',
            ]);
        }

        if (! $this->orderAllowsRefund($order)) {
            throw ValidationException::withMessages([
                'order' => 'Refunds can only be issued for cancelled, returned, or delivered orders.',
            ]);
        }

        return DB::transaction(function () use ($order, $amountCents, $reason, $notes, $restoreStock, $admin): Refund {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($amountCents > $lockedOrder->refundableCents()) {
                throw ValidationException::withMessages([
                    'amount_cents' => 'Refund amount exceeds the remaining refundable balance.',
                ]);
            }

            $refund = Refund::query()->create([
                'order_id' => $lockedOrder->id,
                'amount_cents' => $amountCents,
                'reason' => $reason,
                'status' => RefundStatus::Pending,
                'restore_stock' => $restoreStock,
                'notes' => $notes,
                'processed_by' => $admin?->id,
            ]);

            $paymentResult = $this->payments->refund($lockedOrder, $amountCents);

            if (! $paymentResult['success']) {
                $refund->update(['status' => RefundStatus::Failed]);

                throw ValidationException::withMessages([
                    'payment' => $paymentResult['message'] ?? 'Payment refund failed.',
                ]);
            }

            $newRefundedTotal = $lockedOrder->refunded_cents + $amountCents;
            $isFullRefund = $newRefundedTotal >= $lockedOrder->total_cents;

            $lockedOrder->update([
                'refunded_cents' => $newRefundedTotal,
                'payment_status' => $isFullRefund
                    ? PaymentStatus::Refunded
                    : PaymentStatus::PartiallyRefunded,
            ]);

            $refund->update([
                'status' => RefundStatus::Completed,
                'payment_reference' => $paymentResult['reference'],
                'processed_at' => now(),
            ]);

            if ($restoreStock) {
                $this->restoreOrderStock($lockedOrder->fresh(['items.product']));
            }

            if ($isFullRefund && $lockedOrder->status !== OrderStatus::Refunded) {
                if ($lockedOrder->status->canTransitionTo(OrderStatus::Refunded)) {
                    $this->orders->updateStatus(
                        $lockedOrder->fresh(),
                        OrderStatus::Refunded,
                        $paymentResult['message'] ?? 'Refund processed successfully.',
                        $admin?->name ?? 'Admin',
                    );
                }
            } elseif ($notes) {
                $this->orders->addNote($lockedOrder, 'Refund issued: '.$notes, $admin?->name);
            }

            $this->audit->log(
                AuditAction::OrderRefundProcessed,
                "Refund of {$amountCents} cents processed for order {$lockedOrder->order_number}.",
                subject: $lockedOrder,
                causer: $admin,
                properties: [
                    'refund_id' => $refund->id,
                    'amount_cents' => $amountCents,
                    'payment_reference' => $paymentResult['reference'],
                ],
            );

            return $refund->fresh(['order.customer', 'processedBy']);
        });
    }

    private function orderAllowsRefund(Order $order): bool
    {
        return in_array($order->status, [
            OrderStatus::Cancelled,
            OrderStatus::Returned,
            OrderStatus::Delivered,
            OrderStatus::Pending,
            OrderStatus::Confirmed,
            OrderStatus::Processing,
            OrderStatus::Packed,
            OrderStatus::Shipped,
        ], true);
    }

    private function restoreOrderStock(Order $order): void
    {
        if ($this->inventory->hasReturnMovement($order->order_number)) {
            return;
        }

        if (! $this->inventory->hasSaleMovement($order->order_number)) {
            return;
        }

        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->inventory->restoreForReturn(
                product: $item->product,
                quantity: $item->quantity,
                reference: $order->order_number,
            );
        }
    }

    private function notifyAdminsOfReturnRequest(Order $order): void
    {
        $admins = User::query()
            ->where(function ($query): void {
                $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('slug', 'owner'))
                    ->orWhereHas('roles.permissions', fn ($permissionQuery) => $permissionQuery->where('slug', 'refunds.view'));
            })
            ->get();

        foreach ($admins as $admin) {
            $this->notifications->notifySystemAlert(
                $admin,
                "Return requested — {$order->order_number}",
                ($order->customer?->name ?? 'A customer').' requested a return. Review and process the refund when items are received.',
                route('admin.orders.show', $order),
                'Review order',
            );
        }
    }
}
