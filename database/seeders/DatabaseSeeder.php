<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
//            ProductCategorySeeder::class,
//            ProductSeeder::class,
            PermissionsSeeder::class,
            UserSeeder::class,
            BarCodeSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();
    }
}
