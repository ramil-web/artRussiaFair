<?php

namespace Database\Seeders;

use App\Models\Product;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (DB::table('products')->count()){
            $this->command->error('Products table already has records!');

            return;
        }

        if (DB::table('product_categories')->count() == 0) {
            $this->command->error('product_categories table is empty!');

            return;
        }

        foreach ($this->getProducts() as $product) {
            $properties = $product['properties'] ?? [];
            unset($product['properties']);

            $product['image'] = $this->saveImageForProduct($product['image']);

            /**
             * @var Product $productModel
             */
            $productModel = Product::query()->create($product);

            $productModel->properties()->createMany($properties ?? []);
        }
    }

    private function saveImageForProduct(string $filename): bool|string|null
    {
        try {
            if ($image = $this->getImageFile($filename)) {
                return Storage::disk('s3')
                    ->putFile('uploads/product_images', $image);
            }
        } catch (Exception | Throwable $exception) {
            return null;
        }


        return null;
    }

    private function getImageFile(string $filename): File|bool
    {
        $catalogName = 'database/seeders/files/product_images';

        if (file_exists(sprintf("%s/%s", $catalogName, $filename))) {
            return new File(sprintf("%s/%s", $catalogName, $filename));
        }

        return false;
    }


    private function getProducts(): array
    {
        return json_decode($this->getFile(), true);
    }

    private function getPath()
    {
        return 'database/seeders/json_resources/products.json';
    }

    private function getFile()
    {
        return file_get_contents($this->getPath());
    }
}
