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
            $cat = Category::firstOrCreate(['name' => $catName], ['slug' => \Str::slug($catName)]);
        }

        // sample products array — you can expand these names/prices
        $samples = [
            ['name' => 'Logitech G Pro X Headset', 'category' => 'Accessories', 'price' => 129.99, 'stock' => 45],
            ['name' => 'MSI Gaming Laptop i7 RTX 4060', 'category' => 'Laptop', 'price' => 1299.00, 'stock' => 12],
            ['name' => 'Samsung Galaxy Tab S9', 'category' => 'Smartphone', 'price' => 699.00, 'stock' => 20],
            ['name' => 'Razer BlackWidow V3', 'category' => 'Gaming', 'price' => 139.99, 'stock' => 12],
            ['name' => 'MacBook Pro 14"', 'category' => 'Mac', 'price' => 1999.00, 'stock' => 8],
            ['name' => 'Adobe Photoshop License', 'category' => 'Software', 'price' => 20.00, 'stock' => 9999],
        ];

        $sellerUser = User::where('role', 'seller')->first();
        // ensure some seller store(s) exist (if not, create a dummy store)
        if (!$sellerUser) {
            $store = SellerStore::create([
                'user_id' => User::where('role','seller')->first()->id ?? null,
                'store_name' => 'Demo Store',
                'status' => 'active',
                'created_at' => now(),
            ]);
        }

        $store = SellerStore::firstOrCreate(
        ['user_id' => $sellerUser->id],
        [
            'store_name' => 'Demo Store',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
            ]
        );



        foreach ($samples as $s) {
            $cat = Category::where('name', $s['category'])->first();
            Product::create([
                'seller_store_id' => $store->id,
                'category_id' => $cat->id,
                'name' => $s['name'],
                'description' => $s['name'] . ' — demo product for seeder.',
                'original_price' => $s['price'],
                'stock' => $s['stock'],
                'status' => 'active',
                'brand'=>'DemoBrand',
                'images' => '/images/sample/' . \Str::slug($s['name']) . '.jpg',
                'created_at' => now()->subDays(rand(1,120)),
                'verified_at' => now()->subDays(rand(1,100)),
                'verified_by' => User::where('role','admin')->inRandomOrder()->first()->id,
            ]);
        }

        // create some random products too
        Product::factory(20)->create();
    }
}
