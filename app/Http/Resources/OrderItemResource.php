<?php

namespace App\Http\Resources;

use App\Models\OrderItem;
use App\Support\MoneyFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OrderItem */
class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;

        return [
            'name' => $product?->name ?? 'Product',
            'sku' => $product?->sku,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_price' => MoneyFormatter::format($this->unit_price_cents),
            'subtotal' => MoneyFormatter::format($this->line_total_cents),
            'image' => $product?->primaryImageUrl(),
            'url' => $product ? route('product.show', $product) : route('shop'),
        ];
    }
}
