<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->city();

        return [
            'name' => $city.' Warehouse',
            'code' => strtoupper(fake()->unique()->lexify('WH-???')),
            'city' => $city,
            'state' => fake()->stateAbbr(),
            'country' => config('store.country_code', 'BD'),
            'address' => fake()->streetAddress(),
            'is_default' => false,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (): array => [
            'is_default' => true,
            'sort_order' => 0,
        ]);
    }
}
