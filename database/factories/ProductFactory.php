<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'brand_id' => null,
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'price_cents' => fake()->numberBetween(2500, 45000),
            'stock_quantity' => fake()->numberBetween(0, 120),
            'low_stock_threshold' => 10,
            'is_active' => true,
        ];
    }

    public function forBrand(Brand $brand): static
    {
        return $this->state(fn (): array => [
            'brand_id' => $brand->id,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (): array => [
            'stock_quantity' => fake()->numberBetween(1, 10),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => [
            'stock_quantity' => 0,
        ]);
    }
}
