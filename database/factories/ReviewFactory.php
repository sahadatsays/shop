<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'customer_id' => null,
            'author_name' => fake()->name(),
            'rating' => fake()->numberBetween(4, 5),
            'title' => fake()->optional()->sentence(4),
            'body' => fake()->paragraph(),
            'is_approved' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'is_approved' => false,
        ]);
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn (): array => [
            'customer_id' => $customer->id,
            'author_name' => $customer->name,
        ]);
    }
}
