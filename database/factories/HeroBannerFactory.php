<?php

namespace Database\Factories;

use App\Models\HeroBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeroBanner>
 */
class HeroBannerFactory extends Factory
{
    protected $model = HeroBanner::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'subtitle' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'badge_text' => fake()->optional()->words(3, true),
            'desktop_image_path' => 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=2000&q=75&auto=format&fit=crop',
            'mobile_image_path' => 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=800&q=70&auto=format&fit=crop',
            'primary_label' => 'Shop now',
            'primary_url' => '/shop',
            'secondary_label' => 'Our story',
            'secondary_url' => '/about',
            'starts_at' => null,
            'ends_at' => null,
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

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);
    }
}
