<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    protected $model = PurchaseItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = fake()->numberBetween(1, 20);
        $unitCost = fake()->numberBetween(100, 5000);
        $discount = 0;
        $tax = 0;

        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => $product->id,
            'sku_snapshot' => $product->sku,
            'product_name_snapshot' => $product->name,
            'quantity_ordered' => $quantity,
            'quantity_received' => 0,
            'unit_cost_cents' => $unitCost,
            'discount_cents' => $discount,
            'tax_cents' => $tax,
            'subtotal_cents' => ($quantity * $unitCost) - $discount + $tax,
            'sort_order' => 0,
        ];
    }
}
