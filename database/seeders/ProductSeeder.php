<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Indomie Goreng', 'price' => 3500, 'stock' => 100],
            ['name' => 'Aqua 600ml', 'price' => 4000, 'stock' => 100],
            ['name' => 'Beras 5kg', 'price' => 75000, 'stock' => 30],
            ['name' => 'Sabun Cuci', 'price' => 12000, 'stock' => 50],
            ['name' => 'Kopi Kapal Api', 'price' => 6000, 'stock' => 60],
            ['name' => 'Teh Botol', 'price' => 5000, 'stock' => 60],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product + ['is_active' => true]
            );
        }
    }
}
