<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount_cents' => fake()->numberBetween(1000, 20000),
            'method' => PaymentMethod::Cash,
            'status' => PaymentStatus::Paid,
            'transaction_reference' => strtoupper(fake()->bothify('TX-####??')),
            'paid_at' => now(),
            'received_by' => null,
            'notes' => null,
        ];
    }
}
