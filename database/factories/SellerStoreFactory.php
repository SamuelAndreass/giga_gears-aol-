<?php

namespace Database\Factories;

use App\Models\SellerStore;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerStoreFactory extends Factory
{
    protected $model = SellerStore::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'store_name' => $this->faker->company(),
            'store_address' => $this->faker->address(),
            'store_logo' => null,
        ];
    }
}
