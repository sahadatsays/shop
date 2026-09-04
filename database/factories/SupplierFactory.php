<?php

namespace Database\Factories;

use App\Enums\SupplierStatus;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Supplies',
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'phone' => fake()->numerify('01#########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'district' => fake()->city(),
            'country' => 'Bangladesh',
            'tax_id' => fake()->optional()->numerify('TIN-########'),
            'notes' => fake()->optional()->sentence(),
            'status' => SupplierStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => SupplierStatus::Inactive,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'status' => SupplierStatus::Active,
        ]);
    }
}
