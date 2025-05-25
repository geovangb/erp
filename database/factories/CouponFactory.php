<?php
/**
 * GB Developer
 *
 * @category GB_Developer
 * @package  GB
 *
 * @copyright Copyright (c) 2025 GB Developer.
 *
 * @author Geovan Brambilla <geovangb@gmail.com>
 */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory
    extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->word),
            'discount' => $this->faker->numberBetween(5, 50),
            'min_cart_value' => $this->faker->numberBetween(50, 200),
            'valid_until' => now()->addDays(5),
        ];
    }
}
