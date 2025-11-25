<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->company();

        return [
            'user_id' => null, // set by seeder
            'name' => $name,
            'address' => $this->faker->address(),
            'avatar_path' => null, // can set to a dummy if desired
        ];
    }
}
