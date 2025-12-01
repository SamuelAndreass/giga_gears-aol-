<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerStore;
use App\Models\User;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Smartphone', 'Laptop', 'Accessories', 'Gaming', 'Mac', 'Software'
        ];

        foreach ($categories as $catName) {
            Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)]
            );
        }

        // pastikan ada seller
        $sellerUser = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Demo Seller',
                'password' => bcrypt('password'),
                'role' => 'seller'
            ]
        );

        $store = SellerStore::firstOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'store_name' => 'Demo Store',
                'status' => 'active',
            ]
        );

        // sample products
        $samples = [
            ['name' => 'Logitech G Pro X Headset', 'category' => 'Accessories', 'price' => 129.99, 'stock' => 45],
            ['name' => 'MSI Gaming Laptop i7 RTX 4060', 'category' => 'Laptop', 'price' => 1299.00, 'stock' => 12],
            ['name' => 'Samsung Galaxy Tab S9', 'category' => 'Smartphone', 'price' => 699.00, 'stock' => 20],
            ['name' => 'Razer BlackWidow V3', 'category' => 'Gaming', 'price' => 139.99, 'stock' => 12],
            ['name' => 'MacBook Pro 14"', 'category' => 'Mac', 'price' => 1999.00, 'stock' => 8],
            ['name' => 'Adobe Photoshop License', 'category' => 'Software', 'price' => 20.00, 'stock' => 9999],
        ];

        foreach ($samples as $s) {
            $cat = Category::where('name', $s['category'])->first();

            Product::create([
                'seller_store_id' => $store->id,
                'category_id' => $cat->id,
                'name' => $s['name'],
                'description' => $s['name'] . ' — demo product for seeder.',
                'original_price' => $s['price'],
                'discount_price' => 0,
                'discount_percentage' => 0,
                'stock' => $s['stock'],
                'brand' => 'DemoBrand',
                'images' => json_encode(['/images/sample/' . Str::slug($s['name']) . '.jpg']),
                'rating' => 4.5,
                'review_count' => 10,
                'is_featured' => false,
                'variants' => json_encode([
                    ['name' => 'Default', 'value' => 'Standard', 'price' => $s['price'], 'stock' => $s['stock']]
                ]),
                'SKU' => strtoupper(Str::random(10)),
                'weight' => 10,
                'diameter' => 2.5,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tambahkan 20 produk random dari factory
        Product::factory(20)->create();
    }
}
