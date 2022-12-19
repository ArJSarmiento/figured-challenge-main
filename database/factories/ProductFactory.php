<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 20);
        return [
            'initial_quantity' => $quantity,
            'quantity' =>  $quantity,
            'unit_price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
