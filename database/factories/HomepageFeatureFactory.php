<?php

namespace Database\Factories;

use App\Models\HomepageFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomepageFeature>
 */
class HomepageFeatureFactory extends Factory
{
    protected $model = HomepageFeature::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => 'shield',
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
