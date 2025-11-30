<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\SellerStore;
use App\Models\Category;
use App\Models\User;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        // price in decimal (12,2) - faker number between
        $price = $this->faker->randomFloat(2, 10, 2000); // adjust range
        return [
            'seller_store_id' => SellerStore::inRandomOrder()->first()->id ?? null,
            'category_id' => Category::inRandomOrder()->first()->id ?? null,
            'name' => $this->faker->randomElement([
                'Logitech G Pro X Headset',
                'Razer BlackWidow V3',
                'MSI Gaming Laptop RTX 4060',
                'Samsung Galaxy Tab S9',
                'HyperX Cloud II',
                'Apple MacBook Pro',
                'Adobe Photoshop License'
            ]),
            'description' => $this->faker->paragraph(),
            'original_price' => $price,
            'stock' => $this->faker->numberBetween(0, 200),
            'status' => 'active',
            'brand' => $this->faker->company(),
            'images' => $this->faker->imageUrl(640, 480, 'technics', true),
            'rating' => $this->faker->randomFloat(1, 3.5, 5),
            'created_at' => now()->subDays($this->faker->numberBetween(0, 180)),
            'updated_at' => now(),
            'verified_at' => now()->subDays($this->faker->numberBetween(0, 150)),
            'verified_by' => User::where('role','admin')->inRandomOrder()->first()->id ?? null,
        ];
    }
}
