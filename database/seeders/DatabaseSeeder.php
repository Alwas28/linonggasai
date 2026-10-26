<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Reference data (categories, information pages) is always seeded.
     * Demo accounts and products are only seeded outside production;
     * production admins are created with `php artisan ldch:create-admin`.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            PageSeeder::class,
        ]);

        if (! app()->isProduction()) {
            $this->call(DemoSeeder::class);
        }
    }
}
