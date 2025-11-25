<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition()
    {
        $title = $this->faker->words(3, true);

        return [
            'seller_store_id' => null, // set by seeder
            'name' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(4),
            'price' => $this->faker->numberBetween(10000, 1000000),
            'stock' => $this->faker->numberBetween(0, 100),
            'description' => $this->faker->paragraph(),
        ];
    }
}
