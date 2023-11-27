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
    public function definition(): array
    {
        $categories = ['Electronic', 'Clothes', 'Home', 'Others'];
        return [
            'category'=>fake()->randomElement($categories),
            'product_name'=>fake()->word(),            
            'product_description'=>fake()->sentence(10),
            'product_price'=>fake()->randomFloat(2, 1, 1000),
            'product_image'=>fake()->image(),

        ];
    }
}
