<?php

namespace Database\Factories;

use App\Enums\PurchasePaymentStatus;
use App\Enums\PurchaseStatus;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Support\PurchaseNumberGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_number' => PurchaseNumberGenerator::generate(),
            'supplier_id' => Supplier::factory(),
            'purchase_date' => now()->toDateString(),
            'expected_delivery_date' => now()->addDays(7)->toDateString(),
            'status' => PurchaseStatus::Draft,
            'payment_status' => PurchasePaymentStatus::Unpaid,
            'notes' => fake()->optional()->sentence(),
            'subtotal_cents' => 0,
            'discount_cents' => 0,
            'shipping_cents' => 0,
            'tax_cents' => 0,
            'grand_total_cents' => 0,
            'paid_cents' => 0,
            'created_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => PurchaseStatus::Approved,
            'submitted_at' => now()->subHour(),
            'approved_at' => now(),
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'status' => PurchaseStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }
}
