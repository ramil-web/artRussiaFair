<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class BarCodeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        $barCodes = $this->getBarCodes();
        foreach ($barCodes as $barCode) {
            $data[] = [
                'barcode'    => $barCode['barcode'],
                'product_id' => $barCode['productId'],
                'codes_2025' => true,
                'created_at' => now()
            ];
        }
        DB::table('broadcasts')
            ->insert($data);
    }

    private function getBarCodes(): array
    {
        return json_decode($this->getFile(), true);
    }


    private function getPath(): string
    {
        return 'database/seeders/json_resources/barCodes_2025.json';
    }

    private function getFile(): bool|string
    {
        return file_get_contents($this->getPath());
    }
}
