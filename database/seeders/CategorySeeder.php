<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Buat kategori default / demo
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Gawai, aksesoris & elektronik rumah'],
            ['name' => 'Pakaian', 'slug' => 'pakaian', 'description' => 'Pakaian pria, wanita & anak'],
            ['name' => 'Kecantikan', 'slug' => 'kecantikan', 'description' => 'Skincare & kosmetik'],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'description' => 'Peralatan rumah & dekor'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'description' => 'Peralatan & pakaian olahraga'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        // Tambahan: buat kategori random jika mau
        Category::factory()->count(5)->create();
    }
}
