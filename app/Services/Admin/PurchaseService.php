<?php

namespace App\Services\Admin;

use App\Enums\AuditAction;
use App\Enums\PurchasePaymentStatus;
use App\Enums\PurchaseStatus;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\AuditService;
use App\Support\PurchaseNumberGenerator;
use App\Support\Purchases\PurchaseTotalsCalculator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(
        private PurchaseTotalsCalculator $totals,
        private SupplierService $suppliers,
        private AuditService $audit,
    ) {}

    /**
     * @param  array{search?: ?string, status?: ?string, supplier_id?: int|null}  $filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Purchase::query()
            ->with(['supplier', 'creator'])
            ->withSum('items as quantity_ordered_sum', 'quantity_ordered')
            ->withSum('items as quantity_received_sum', 'quantity_received')
            ->latest('id');

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('purchase_number', 'like', $term)
                    ->orWhere('notes', 'like', $term)
                    ->orWhereHas('supplier', function ($supplier) use ($term): void {
                        $supplier->where('name', 'like', $term)
                            ->orWhere('company_name', 'like', $term);
                    });
            });
        }

        if (($status = $filters['status'] ?? null) !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($supplierId = $filters['supplier_id'] ?? null) {
            $query->where('supplier_id', $supplierId);
        }

        return $query->paginate(15)->withQueryString();
    }

    public function find(int $id): Purchase
    {
        return Purchase::query()
            ->with([
                'supplier',
                'creator',
                'approver',
                'items.product',
                'receipts.warehouse',
                'receipts.receiver',
                'receipts.items',
            ])
            ->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, User $actor): Purchase
    {
        return DB::transaction(function () use ($data, $actor): Purchase {
            $supplier = Supplier::query()->lockForUpdate()->findOrFail((int) $data['supplier_id']);
            $this->suppliers->assertSelectableForPurchase($supplier);

            $calculated = $this->calculateFromPayload($data);
            $status = ! empty($data['submit'])
                ? PurchaseStatus::Submitted
                : PurchaseStatus::Draft;

            $purchase = Purchase::query()->create([
                'purchase_number' => PurchaseNumberGenerator::generate(),
                'supplier_id' => $supplier->id,
                'purchase_date' => $data['purchase_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $status,
                'payment_status' => PurchasePaymentStatus::Unpaid,
                'notes' => $data['notes'] ?? null,
                'subtotal_cents' => $calculated['subtotal_cents'],
                'discount_cents' => $calculated['discount_cents'],
                'shipping_cents' => $calculated['shipping_cents'],
                'tax_cents' => $calculated['tax_cents'],
                'grand_total_cents' => $calculated['grand_total_cents'],
                'paid_cents' => 0,
                'created_by' => $actor->id,
                'submitted_at' => $status === PurchaseStatus::Submitted ? now() : null,
            ]);

            $this->syncItems($purchase, $data['items'], $calculated['lines']);

            $purchase = $this->find($purchase->id);

            $this->audit->log(
                AuditAction::PurchaseCreated,
                "Purchase {$purchase->purchase_number} created.",
                subject: $purchase,
                causer: $actor,
                properties: [
                    'purchase_number' => $purchase->purchase_number,
                    'status' => $purchase->status->value,
                    'grand_total_cents' => $purchase->grand_total_cents,
                ],
            );

            return $purchase;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Purchase $purchase, array $data, User $actor): Purchase
    {
        return DB::transaction(function () use ($purchase, $data, $actor): Purchase {
            $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);

            if (! $purchase->status->isEditable()) {
                throw ValidationException::withMessages([
                    'purchase' => 'Only draft purchases can be edited.',
                ]);
            }

            $supplier = Supplier::query()->lockForUpdate()->findOrFail((int) $data['supplier_id']);
            $this->suppliers->assertSelectableForPurchase($supplier);

            $calculated = $this->calculateFromPayload($data);

            $purchase->update([
                'supplier_id' => $supplier->id,
                'purchase_date' => $data['purchase_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal_cents' => $calculated['subtotal_cents'],
                'discount_cents' => $calculated['discount_cents'],
                'shipping_cents' => $calculated['shipping_cents'],
                'tax_cents' => $calculated['tax_cents'],
                'grand_total_cents' => $calculated['grand_total_cents'],
            ]);

            $purchase->items()->delete();
            $this->syncItems($purchase, $data['items'], $calculated['lines']);

            $purchase = $this->find($purchase->id);

            $this->audit->log(
                AuditAction::PurchaseUpdated,
                "Purchase {$purchase->purchase_number} updated.",
                subject: $purchase,
                causer: $actor,
                properties: [
                    'purchase_number' => $purchase->purchase_number,
                    'grand_total_cents' => $purchase->grand_total_cents,
                ],
            );

            return $purchase;
        });
    }

    public function submit(Purchase $purchase, User $actor): Purchase
    {
        return DB::transaction(function () use ($purchase, $actor): Purchase {
            $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);

            if (! $purchase->status->canSubmit()) {
                throw ValidationException::withMessages([
                    'purchase' => 'Only draft purchases can be submitted.',
                ]);
            }

            $this->assertHasItems($purchase);

            $purchase->update([
                'status' => PurchaseStatus::Submitted,
                'submitted_at' => now(),
            ]);

            $purchase = $this->find($purchase->id);

            $this->audit->log(
                AuditAction::PurchaseSubmitted,
                "Purchase {$purchase->purchase_number} submitted for approval.",
                subject: $purchase,
                causer: $actor,
            );

            return $purchase;
        });
    }

    public function approve(Purchase $purchase, User $actor): Purchase
    {
        return DB::transaction(function () use ($purchase, $actor): Purchase {
            $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);

            if (! $purchase->status->canApprove()) {
                throw ValidationException::withMessages([
                    'purchase' => 'This purchase cannot be approved in its current status.',
                ]);
            }

            $this->assertHasItems($purchase);

            $purchase->update([
                'status' => PurchaseStatus::Approved,
                'approved_by' => $actor->id,
                'approved_at' => now(),
                'submitted_at' => $purchase->submitted_at ?? now(),
            ]);

            $purchase = $this->find($purchase->id);

            $this->audit->log(
                AuditAction::PurchaseApproved,
                "Purchase {$purchase->purchase_number} approved.",
                subject: $purchase,
                causer: $actor,
            );

            return $purchase;
        });
    }

    public function cancel(Purchase $purchase, User $actor): Purchase
    {
        return DB::transaction(function () use ($purchase, $actor): Purchase {
            $purchase = Purchase::query()->with('items')->lockForUpdate()->findOrFail($purchase->id);

            if (! $purchase->status->canCancel()) {
                throw ValidationException::withMessages([
                    'purchase' => 'This purchase cannot be cancelled in its current status.',
                ]);
            }

            if ($purchase->hasReceivedStock()) {
                throw ValidationException::withMessages([
                    'purchase' => 'Purchases with received stock cannot be cancelled. Use a purchase return instead.',
                ]);
            }

            $purchase->update([
                'status' => PurchaseStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            $purchase = $this->find($purchase->id);

            $this->audit->log(
                AuditAction::PurchaseCancelled,
                "Purchase {$purchase->purchase_number} cancelled.",
                subject: $purchase,
                causer: $actor,
            );

            return $purchase;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     lines: list<array{quantity: int, unit_cost_cents: int, discount_cents: int, tax_cents: int, subtotal_cents: int}>,
     *     subtotal_cents: int,
     *     discount_cents: int,
     *     shipping_cents: int,
     *     tax_cents: int,
     *     grand_total_cents: int
     * }
     */
    private function calculateFromPayload(array $data): array
    {
        $lineInputs = [];

        foreach ($data['items'] as $item) {
            $lineInputs[] = [
                'quantity' => (int) $item['quantity'],
                'unit_cost_cents' => (int) $item['unit_cost_cents'],
                'discount_cents' => (int) ($item['discount_cents'] ?? 0),
                'tax_cents' => (int) ($item['tax_cents'] ?? 0),
            ];
        }

        return $this->totals->calculate(
            $lineInputs,
            (int) ($data['discount_cents'] ?? 0),
            (int) ($data['shipping_cents'] ?? 0),
            (int) ($data['tax_cents'] ?? 0),
        );
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @param  list<array{quantity: int, unit_cost_cents: int, discount_cents: int, tax_cents: int, subtotal_cents: int}>  $calculatedLines
     */
    private function syncItems(Purchase $purchase, array $items, array $calculatedLines): void
    {
        foreach ($items as $index => $item) {
            $product = Product::query()->withTrashed()->lockForUpdate()->findOrFail((int) $item['product_id']);
            $line = $calculatedLines[$index];

            PurchaseItem::query()->create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'sku_snapshot' => $product->sku,
                'product_name_snapshot' => $product->name,
                'quantity_ordered' => $line['quantity'],
                'quantity_received' => 0,
                'unit_cost_cents' => $line['unit_cost_cents'],
                'discount_cents' => $line['discount_cents'],
                'tax_cents' => $line['tax_cents'],
                'subtotal_cents' => $line['subtotal_cents'],
                'sort_order' => $index,
            ]);
        }
    }

    private function assertHasItems(Purchase $purchase): void
    {
        if ($purchase->items()->count() === 0) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one product before continuing.',
            ]);
        }
    }
}
