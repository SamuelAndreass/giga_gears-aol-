<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // assume Users migration & seeder (users) already exist
        $this->call([
            InitialUsersSeeder::class,
            CategoryProductSeeder::class,
            ShippingSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
