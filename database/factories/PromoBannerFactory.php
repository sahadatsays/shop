<?php

namespace Database\Factories;

use App\Enums\PromoBannerLayout;
use App\Models\PromoBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoBanner>
 */
class PromoBannerFactory extends Factory
{
    protected $model = PromoBanner::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layout' => PromoBannerLayout::Single,
            'title' => fake()->sentence(3),
            'image_path' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=1200&q=75&auto=format&fit=crop',
            'button_label' => 'Shop collection',
            'url' => '/shop',
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
