<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;

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

        // Create associated store for this seller
        $store = Store::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Demo Seller Store',
            'address' => 'Jl. Demo No.1, Jakarta',
            // avatar_path left null; you can set a dummy path if you copy image to storage
        ]);
    }
}
