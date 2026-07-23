<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
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
        $slug = Str::slug($name);

        return [
            'category_id' => Category::factory(),
            'brand_id' => null,
            'name' => ucwords($name),
            'slug' => $slug,
            'sku' => strtoupper(Str::replace('-', '', $slug)).'-'.fake()->unique()->numerify('####'),
            'barcode' => fake()->optional(0.4)->ean13(),
            'short_description' => fake()->optional()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'price_cents' => fake()->numberBetween(2500, 45000),
            'stock_quantity' => fake()->numberBetween(0, 120),
            'low_stock_threshold' => 10,
            'status' => ProductStatus::Published,
            'is_featured' => false,
            'is_new_arrival' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function forBrand(Brand $brand): static
    {
        return $this->state(fn (): array => [
            'brand_id' => $brand->id,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'is_featured' => true,
        ]);
    }

    public function newArrival(): static
    {
        return $this->state(fn (): array => [
            'is_new_arrival' => true,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => ProductStatus::Draft,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => ProductStatus::Archived,
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
