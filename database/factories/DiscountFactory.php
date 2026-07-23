<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->bothify('SAVE##')),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'type' => DiscountType::Percent,
            'value' => fake()->numberBetween(5, 25),
            'min_order_cents' => fake()->optional()->numberBetween(2500, 10000),
            'max_uses' => fake()->optional()->numberBetween(50, 1000),
            'used_count' => 0,
            'starts_at' => now()->subWeek(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
        ];
    }
}
