<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(5000, 50000);

        return [
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'subtotal_cents' => $subtotal,
            'total_cents' => $subtotal,
            'placed_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function today(): static
    {
        return $this->state(fn (): array => [
            'placed_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => fake()->randomElement(OrderStatus::pendingStatuses()),
        ]);
    }

    public function placedAt(\DateTimeInterface $date): static
    {
        return $this->state(fn (): array => [
            'placed_at' => $date,
        ]);
    }
}
