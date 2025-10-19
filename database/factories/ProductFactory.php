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
            'name' => $this->faker->words(2, true),   // contoh: "Logitech Mouse"
            'description' => $this->faker->sentence(10),
            'original_price' => $this->faker->randomFloat(2, 50000, 2000000),
            'stock' => $this->faker->numberBetween(5, 50),
            'images' => null, // opsional, kalau ada kolom image
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
