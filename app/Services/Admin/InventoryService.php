<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminInventoryRepositoryInterface;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\AuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    public function __construct(
        private AdminInventoryRepositoryInterface $inventory,
        private AuditService $audit,
    ) {}

    public function listProducts(array $filters = []): LengthAwarePaginator
    {
        return $this->inventory->paginateProducts($filters);
    }

    public function listMovements(array $filters = []): LengthAwarePaginator
    {
        return $this->inventory->paginateMovements($filters);
    }

    public function showProduct(int $id): Product
    {
        return $this->inventory->findProduct($id);
    }

    /**
     * @return array{total: int, in_stock: int, low_stock: int, out_of_stock: int}
     */
    public function summary(): array
    {
        return $this->inventory->summaryCounts();
    }

    public function activeWarehouses(): Collection
    {
        return $this->inventory->activeWarehouses();
    }

    public function defaultWarehouse(): Warehouse
    {
        return Warehouse::query()->active()->where('is_default', true)->first()
            ?? Warehouse::query()->active()->ordered()->firstOrFail();
    }

    public function initializeStock(Product $product, int $quantity, ?Warehouse $warehouse = null): ?StockMovement
    {
        if ($quantity <= 0) {
            return null;
        }

        $warehouse ??= $this->defaultWarehouse();

        return $this->recordMovement(
            product: $product,
            warehouse: $warehouse,
            type: StockMovementType::Initial,
            targetQuantity: $quantity,
            reference: null,
            notes: 'Opening stock on product creation.',
        );
    }

    public function adjustStock(
        Product $product,
        Warehouse $warehouse,
        StockMovementType $type,
        int $quantity,
        ?string $reference = null,
        ?string $notes = null,
    ): StockMovement {
        if (! in_array($type, StockMovementType::adjustable(), true)) {
            throw new InvalidArgumentException('Invalid adjustment type.');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        $warehouseStock = $this->warehouseStock($product, $warehouse);
        $current = $warehouseStock->quantity;

        $targetQuantity = match ($type) {
            StockMovementType::AdjustmentIn => $current + $quantity,
            StockMovementType::AdjustmentOut => max(0, $current - $quantity),
            StockMovementType::Recount => $quantity,
        };

        if ($type === StockMovementType::AdjustmentOut && $quantity > $current) {
            throw new InvalidArgumentException('Cannot decrease more than available warehouse stock.');
        }

        return $this->recordMovement(
            product: $product,
            warehouse: $warehouse,
            type: $type,
            targetQuantity: $targetQuantity,
            reference: $reference,
            notes: $notes,
        );
    }

    public function syncProductStockFromForm(Product $product, int $newTotal, ?string $notes = null): ?StockMovement
    {
        $warehouse = $this->defaultWarehouse();
        $currentTotal = $product->stock_quantity;

        if ($newTotal === $currentTotal) {
            return null;
        }

        $warehouseStock = $this->warehouseStock($product, $warehouse);
        $difference = $newTotal - $currentTotal;
        $targetWarehouseQuantity = max(0, $warehouseStock->quantity + $difference);

        return $this->recordMovement(
            product: $product,
            warehouse: $warehouse,
            type: StockMovementType::Recount,
            targetQuantity: $targetWarehouseQuantity,
            reference: null,
            notes: $notes ?? 'Stock updated via product form.',
            syncProductTotal: $newTotal,
        );
    }

    /**
     * Deduct sale quantity from warehouses that actually hold stock (default first).
     *
     * @return Collection<int, StockMovement>
     */
    public function deductForSale(Product $product, int $quantity, string $reference, ?string $notes = null): Collection
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($product, $quantity, $reference, $notes): Collection {
            $this->ensureWarehouseStockInitialized($product);

            $defaultWarehouseId = $this->defaultWarehouse()->id;

            $stocks = WarehouseStock::query()
                ->where('product_id', $product->id)
                ->where('quantity', '>', 0)
                ->lockForUpdate()
                ->get()
                ->sortBy(fn (WarehouseStock $stock): int => $stock->warehouse_id === $defaultWarehouseId ? 0 : 1)
                ->values();

            $available = (int) $stocks->sum('quantity');

            if ($quantity > $available) {
                throw new InvalidArgumentException('Insufficient warehouse stock for sale.');
            }

            $remaining = $quantity;
            $movements = collect();

            foreach ($stocks as $stock) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($remaining, $stock->quantity);
                $warehouse = Warehouse::query()->findOrFail($stock->warehouse_id);

                $movements->push($this->recordMovement(
                    product: $product,
                    warehouse: $warehouse,
                    type: StockMovementType::Sale,
                    targetQuantity: $stock->quantity - $take,
                    reference: $reference,
                    notes: $notes ?? 'Stock deducted for order '.$reference.'.',
                ));

                $remaining -= $take;
            }

            return $movements;
        });
    }

    /**
     * Restore returned quantity into the warehouses that originally sold for this reference.
     *
     * @return Collection<int, StockMovement>
     */
    public function restoreForReturn(Product $product, int $quantity, string $reference, ?string $notes = null): Collection
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($product, $quantity, $reference, $notes): Collection {
            $saleMovements = StockMovement::query()
                ->where('product_id', $product->id)
                ->where('reference', $reference)
                ->where('type', StockMovementType::Sale)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($saleMovements->isEmpty()) {
                throw new InvalidArgumentException('No sale movement found to restore stock against.');
            }

            $alreadyRestored = (int) abs((float) StockMovement::query()
                ->where('product_id', $product->id)
                ->where('reference', $reference)
                ->where('type', StockMovementType::Return)
                ->sum('quantity_change'));

            $sold = (int) abs((float) $saleMovements->sum('quantity_change'));
            $restorable = max(0, $sold - $alreadyRestored);

            if ($quantity > $restorable) {
                throw new InvalidArgumentException('Cannot restore more stock than was sold for this order.');
            }

            $remaining = $quantity;
            $movements = collect();

            foreach ($saleMovements as $sale) {
                if ($remaining <= 0) {
                    break;
                }

                $soldFromWarehouse = (int) abs($sale->quantity_change);
                $restoredForWarehouse = (int) abs((float) StockMovement::query()
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->where('reference', $reference)
                    ->where('type', StockMovementType::Return)
                    ->sum('quantity_change'));

                $capacity = max(0, $soldFromWarehouse - $restoredForWarehouse);

                if ($capacity <= 0) {
                    continue;
                }

                $putBack = min($remaining, $capacity);
                $warehouse = Warehouse::query()->findOrFail($sale->warehouse_id);
                $warehouseStock = $this->warehouseStock($product, $warehouse);

                $movements->push($this->recordMovement(
                    product: $product,
                    warehouse: $warehouse,
                    type: StockMovementType::Return,
                    targetQuantity: $warehouseStock->quantity + $putBack,
                    reference: $reference,
                    notes: $notes ?? 'Stock restored from return on order '.$reference.'.',
                ));

                $remaining -= $putBack;
            }

            return $movements;
        });
    }

    public function soldQuantityForReference(Product $product, string $reference): int
    {
        return (int) abs((float) StockMovement::query()
            ->where('product_id', $product->id)
            ->where('reference', $reference)
            ->where('type', StockMovementType::Sale)
            ->sum('quantity_change'));
    }

    public function restoredQuantityForReference(Product $product, string $reference): int
    {
        return (int) abs((float) StockMovement::query()
            ->where('product_id', $product->id)
            ->where('reference', $reference)
            ->where('type', StockMovementType::Return)
            ->sum('quantity_change'));
    }

    public function hasSaleMovement(string $reference): bool
    {
        return StockMovement::query()
            ->where('reference', $reference)
            ->where('type', StockMovementType::Sale)
            ->exists();
    }

    public function hasReturnMovement(string $reference): bool
    {
        return StockMovement::query()
            ->where('reference', $reference)
            ->where('type', StockMovementType::Return)
            ->exists();
    }

    private function ensureWarehouseStockInitialized(Product $product): void
    {
        $hasRows = WarehouseStock::query()
            ->where('product_id', $product->id)
            ->exists();

        if ($hasRows || $product->stock_quantity <= 0) {
            return;
        }

        $this->initializeStock($product, $product->stock_quantity);
    }

    private function recordMovement(
        Product $product,
        Warehouse $warehouse,
        StockMovementType $type,
        int $targetQuantity,
        ?string $reference,
        ?string $notes,
        ?int $syncProductTotal = null,
    ): StockMovement {
        return DB::transaction(function () use ($product, $warehouse, $type, $targetQuantity, $reference, $notes, $syncProductTotal): StockMovement {
            $warehouseStock = $this->warehouseStock($product, $warehouse);
            $quantityBefore = $warehouseStock->quantity;
            $quantityChange = $targetQuantity - $quantityBefore;

            $warehouseStock->update(['quantity' => $targetQuantity]);

            $movement = StockMovement::query()->create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'type' => $type,
                'quantity_change' => $quantityChange,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $targetQuantity,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            if ($syncProductTotal !== null) {
                $product->update(['stock_quantity' => $syncProductTotal]);
            } else {
                $this->syncProductTotal($product);
            }

            $movement->load('product');
            $this->audit->logStockChanged($movement);

            return $movement;
        });
    }

    private function warehouseStock(Product $product, Warehouse $warehouse): WarehouseStock
    {
        return WarehouseStock::query()->firstOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $warehouse->id],
            ['quantity' => 0],
        );
    }

    private function syncProductTotal(Product $product): void
    {
        $total = WarehouseStock::query()
            ->where('product_id', $product->id)
            ->sum('quantity');

        $product->update(['stock_quantity' => (int) $total]);
    }
}
