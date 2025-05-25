<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->ean8,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price_for' => $this->faker->randomFloat(2, 10, 500),
            'price_of' => $this->faker->randomFloat(2, 5, 400),
            'status' => true,
        ];
    }
}
