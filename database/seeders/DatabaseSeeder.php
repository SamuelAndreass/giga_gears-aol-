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
        // ... existing seeder calls (jika ada)
        $this->call([
            \Database\Seeders\InitialUsersSeeder::class,
            \Database\Seeders\ProductSeeder::class,
        ]);

    }
}