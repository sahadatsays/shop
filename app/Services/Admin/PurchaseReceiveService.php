<?php

namespace App\Services\Admin;

use App\Enums\AuditAction;
use App\Enums\PurchaseStatus;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseReceiveService
{
    public function __construct(
        private InventoryService $inventory,
        private PurchaseService $purchases,
        private AuditService $audit,
    ) {}

    /**
     * @param  array{
     *     warehouse_id: int,
     *     idempotency_key: string,
     *     notes?: ?string,
     *     items: list<array{purchase_item_id: int, quantity: int}>
     * }  $data
     */
    public function receive(Purchase $purchase, array $data, User $actor): Purchase
    {
        return DB::transaction(function () use ($purchase, $data, $actor): Purchase {
            $existing = PurchaseReceipt::query()
                ->where('idempotency_key', $data['idempotency_key'])
                ->first();

            if ($existing !== null) {
                return $this->purchases->find($existing->purchase_id);
            }

            $purchase = Purchase::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($purchase->id);

            if (! $purchase->status->canReceive()) {
                throw ValidationException::withMessages([
                    'purchase' => 'Only approved or partially received purchases can receive stock.',
                ]);
            }

            $warehouse = Warehouse::query()
                ->where('is_active', true)
                ->lockForUpdate()
                ->findOrFail((int) $data['warehouse_id']);

            $receiveLines = collect($data['items'])
                ->map(fn (array $line): array => [
                    'purchase_item_id' => (int) $line['purchase_item_id'],
                    'quantity' => (int) $line['quantity'],
                ])
                ->filter(fn (array $line): bool => $line['quantity'] > 0)
                ->values();

            if ($receiveLines->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Enter at least one quantity greater than zero to receive.',
                ]);
            }

            $itemsById = $purchase->items->keyBy('id');

            foreach ($receiveLines as $line) {
                /** @var PurchaseItem|null $item */
                $item = $itemsById->get($line['purchase_item_id']);

                if ($item === null) {
                    throw ValidationException::withMessages([
                        'items' => 'One or more purchase items are invalid for this purchase.',
                    ]);
                }

                $remaining = $item->quantityRemaining();

                if ($line['quantity'] > $remaining) {
                    throw ValidationException::withMessages([
                        'items' => "Cannot receive more than remaining quantity for {$item->product_name_snapshot} (remaining {$remaining}).",
                    ]);
                }
            }

            $receipt = PurchaseReceipt::query()->create([
                'purchase_id' => $purchase->id,
                'warehouse_id' => $warehouse->id,
                'received_by' => $actor->id,
                'idempotency_key' => $data['idempotency_key'],
                'notes' => $data['notes'] ?? null,
                'received_at' => now(),
            ]);

            foreach ($receiveLines as $line) {
                /** @var PurchaseItem $item */
                $item = PurchaseItem::query()->lockForUpdate()->findOrFail($line['purchase_item_id']);
                $product = Product::query()->withTrashed()->lockForUpdate()->findOrFail($item->product_id);

                $movement = $this->inventory->receiveForPurchase(
                    product: $product,
                    warehouse: $warehouse,
                    quantity: $line['quantity'],
                    reference: $purchase->purchase_number,
                    notes: "Purchase {$purchase->purchase_number} receipt #{$receipt->id}",
                    unitCostCents: $item->unit_cost_cents,
                );

                PurchaseReceiptItem::query()->create([
                    'purchase_receipt_id' => $receipt->id,
                    'purchase_item_id' => $item->id,
                    'product_id' => $product->id,
                    'quantity_received' => $line['quantity'],
                    'unit_cost_cents' => $item->unit_cost_cents,
                    'stock_movement_id' => $movement->id,
                ]);

                $item->update([
                    'quantity_received' => $item->quantity_received + $line['quantity'],
                ]);
            }

            $purchase->load('items');

            $status = $purchase->isFullyReceived()
                ? PurchaseStatus::Completed
                : PurchaseStatus::PartiallyReceived;

            $purchase->update([
                'status' => $status,
                'completed_at' => $status === PurchaseStatus::Completed ? now() : null,
            ]);

            $this->audit->log(
                AuditAction::PurchaseReceived,
                "Stock received for purchase {$purchase->purchase_number}.",
                subject: $purchase,
                causer: $actor,
                properties: [
                    'purchase_number' => $purchase->purchase_number,
                    'receipt_id' => $receipt->id,
                    'warehouse_id' => $warehouse->id,
                    'status' => $status->value,
                    'lines' => $receiveLines->all(),
                ],
            );

            return $this->purchases->find($purchase->id);
        });
    }
}
