<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\PurchaseReceipt;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PurchaseReceipt>
 */
class PurchaseReceiptFactory extends Factory
{
    protected $model = PurchaseReceipt::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'warehouse_id' => Warehouse::factory(),
            'received_by' => User::factory(),
            'idempotency_key' => (string) Str::uuid(),
            'notes' => null,
            'received_at' => now(),
        ];
    }
}
