<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SellerStore;

class InitialUsersSeeder extends Seeder
{
    public function run()
    {
        // 1) Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            // password already set in factory to 'password'
        ]);

        // 2) Customer
        $customer = User::factory()->customer()->create([
            'name' => 'Customer Demo',
            'email' => 'customer@example.com',
        ]);

        // 3) Seller + Store (1:1)
        $seller = User::factory()->seller()->create([
            'name' => 'Seller Demo',
            'email' => 'seller@example.com',
        ]);

        \App\Models\CustomerProfile::firstOrCreate([
            'user_id' => $seller->id
        ]);

        // Create associated store for this seller
        $store = SellerStore::factory()->create([
            'user_id' => $seller->id,
            'store_name' => 'Demo Seller Store',
            'store_address' => 'Jl. Demo No.1, Jakarta',
            // store_logo left null; you can set a dummy path if you copy image to storage
        ]);
    }
}
