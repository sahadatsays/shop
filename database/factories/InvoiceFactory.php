<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use App\Support\InvoiceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'invoice_number' => InvoiceNumberGenerator::generate(),
            'issued_at' => now(),
            'snapshot' => [
                'order_number' => 'VS-TEST',
                'items' => [],
                'totals' => [
                    'subtotal_cents' => 0,
                    'total_cents' => 0,
                    'paid_cents' => 0,
                    'due_cents' => 0,
                ],
            ],
        ];
    }
}
