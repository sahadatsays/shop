<?php

namespace Database\Factories;

use App\Models\PurchaseItem;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseReceiptItem>
 */
class PurchaseReceiptItemFactory extends Factory
{
    protected $model = PurchaseReceiptItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = PurchaseItem::factory()->create();

        return [
            'purchase_receipt_id' => PurchaseReceipt::factory(),
            'purchase_item_id' => $item->id,
            'product_id' => $item->product_id,
            'quantity_received' => 1,
            'unit_cost_cents' => $item->unit_cost_cents,
            'stock_movement_id' => null,
        ];
    }
}
