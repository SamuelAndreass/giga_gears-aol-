<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SellerStore;
use App\Models\StoreReview;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 🧱 1️⃣ Kategori
        $categories = [
            ['name' => 'Smartphone', 'slug' => 'smartphone'],
            ['name' => 'Laptop', 'slug' => 'laptop'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
            ['name' => 'Gaming', 'slug' => 'gaming'],
            ['name' => 'Audio', 'slug' => 'audio'],
            ['name' => 'Tablet', 'slug' => 'tablet'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // 👤 2️⃣ Buat User (Customer, Seller, Admin)
        $usersData = [
            [
                'name' => 'test_customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'is_seller' => false,
            ],
            [
                'name' => 'test_seller',
                'email' => 'seller@example.com',
                'role' => 'seller',
                'is_seller' => true,
                'password' => Hash::make('seller123'),
            ],
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_seller' => false,
            ]
        ];

        foreach ($usersData as $userData) {
            $user = User::create($userData);
            if ($user->is_seller) {
                SellerStore::create([
                    'user_id' => $user->id,
                    'store_name' => 'Test Store',
                    'store_phone' => '081234567890',
                    'store_address' => 'Jl. Contoh No. 123, Jakarta',
                ]);
            }
        }

        // 🏪 3️⃣ Ambil profil seller & customer
        $sellerProfile = SellerStore::first();
        $customerProfile = User::where('role', 'customer')->first();

        // 🧰 4️⃣ Produk Dummy
        $products = [
            [
                'name' => 'Logitech G Pro X Headset',
                'category_id' => 4,
                'seller_store_id' => $sellerProfile->id,
                'original_price' => 129.00,
                'discount_price' => 99.00,
                'brand' => 'Logitech',
                'description' => 'The Logitech G Pro X Gaming Headset is designed in collaboration with esports professionals...',
                'images' => ['https://via.placeholder.com/600x600/3B82F6/FFFFFF?text=Logitech+G+Pro+X'],
                'colors' => [
                    ['name' => 'Black', 'code' => '#000000'],
                    ['name' => 'White', 'code' => '#FFFFFF']
                ],
                'specifications' => [
                    'Driver' => 'Hybrid mesh PRO-G 50 mm',
                    'Frequency Response' => '20 Hz – 20 kHz',
                    'Impedance' => '35 Ohms',
                    'Microphone' => 'Detachable Cardioid Unidirectional',
                    'Connection Type' => 'Wired (3.5 mm / USB sound card)',
                    'Weight' => '320 g (without cable)'
                ],
                'features' => [
                    'Advanced 7.1 surround sound',
                    'Blue VO!CE technology',
                    'Durable build quality',
                    'Detachable microphone',
                    'Comfortable memory foam ear pads'
                ],
                'stock' => 25,
                'rating' => 4.8,
                'review_count' => 120,
                'is_featured' => true,
            ],
            [
                'name' => 'MSI Gaming Laptop I7 RTX 4090',
                'category_id' => 2,
                'seller_store_id' => $sellerProfile->id,
                'original_price' => 2499.00,
                'discount_price' => 2199.00,
                'brand' => 'MSI',
                'description' => 'High-performance gaming laptop with Intel i7 processor and RTX 4090 graphics card.',
                'images' => ['https://via.placeholder.com/600x600/8B5CF6/FFFFFF?text=MSI+Gaming+Laptop'],
                'specifications' => [
                    'Processor' => 'Intel Core i7-13700H',
                    'Graphics' => 'NVIDIA GeForce RTX 4090',
                    'RAM' => '32GB DDR5',
                    'Storage' => '1TB NVMe SSD',
                ],
                'features' => ['RGB Keyboard', 'Advanced cooling'],
                'stock' => 8,
                'rating' => 4.7,
                'review_count' => 89,
                'is_featured' => true,
            ]
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            if ($product->id == 1) {
                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $customerProfile->id,
                    'rating' => 5,
                    'comment' => 'Great headset for gaming — 5 stars!',
                    'is_verified' => true
                ]);
            }
        }

 
        StoreReview::create([
            'seller_store_id' => $sellerProfile->id,
            'user_id' => $customerProfile->id,
            'rating' => 5,
            'comment' => 'Pelayanan cepat dan ramah!',
            'is_verified' => true,
        ]);

        $cart = Cart::firstOrCreate(
            ['user_id' => $customerProfile->id, 'status' => 'active'],
            ['total_price' => 0]
        );

 
        $total = 0;
        foreach (Product::all() as $product) {
            $qty = rand(1, 3);
            $subtotal = $product->discount_price * $qty;
            $total += $subtotal;

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $product->discount_price,
            ]);
        }
 
        $cart->update(['total_price' => $total]);

        $this->command->info("✅ Seeder selesai: Users, Products, Reviews, dan Cart berhasil dibuat!");
    }
}
