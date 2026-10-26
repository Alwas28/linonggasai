<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'pertanian', 'name' => 'Pertanian', 'icon' => '🌾', 'description' => 'Beras, sayur, bibit, dan hasil kebun warga Desa Linonggasai.'],
            ['slug' => 'perikanan', 'name' => 'Perikanan', 'icon' => '🐟', 'description' => 'Ikan segar hasil budidaya kolam warga desa.'],
            ['slug' => 'umkm', 'name' => 'UMKM', 'icon' => '🏪', 'description' => 'Produk olahan, kios, dan jasa usaha mikro warga.'],
        ];

        foreach ($categories as $order => $category) {
            Category::query()->firstOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'sort_order' => $order + 1, 'status' => 'active'],
            );
        }
    }
}
