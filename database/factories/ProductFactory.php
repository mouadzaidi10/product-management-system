<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,  // Use a sentence for a more realistic product name
            'sku' => $this->faker->unique()->word,  // Keep word for SKU
            'status' => 'active',
            'price' => $this->faker->randomFloat(2, 10, 100),
            'currency' => 'USD',
        ];
    }
}
