<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Store;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Ambil seller store (asumsi hanya ada satu demo store)
        $store = Store::first();

        if (! $store) {
            $this->command->info('No store found, skipping ProductSeeder.');
            return;
        }

        $total = 20;

        // Create majority products for this store
        $forStore = intval($total * 0.75);

        for ($i = 0; $i < $forStore; $i++) {
            Product::factory()->create([
                'seller_store_id' => $store->id,
            ]);
        }

        // Create remaining products (no seller or random)
        $remaining = $total - $forStore;
        for ($i = 0; $i < $remaining; $i++) {
            Product::factory()->create([
                // If you want them without seller, set seller_store_id => null
                'seller_store_id' => $store->id, // or null if you prefer
            ]);
        }
    }
}
