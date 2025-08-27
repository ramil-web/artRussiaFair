<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (DB::table('product_categories')->count()){
            $this->command->error('product_categories table already has records!');

            return;
        }

        DB::table('product_categories')->insert($this->getProductCategories());
    }

    private function getProductCategories(): array
    {
        return json_decode($this->getFile(), true);
    }


    private function getPath()
    {
        return 'database/seeders/json_resources/product_categories.json';
    }

    private function getFile()
    {
        return file_get_contents($this->getPath());
    }
}
