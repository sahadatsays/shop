<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('+1##########'),
            'password' => 'password',
            'status' => CustomerStatus::Active,
            'internal_notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => CustomerStatus::Inactive,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => [
            'status' => CustomerStatus::Suspended,
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (): array => [
            'status' => CustomerStatus::Blocked,
        ]);
    }
}
