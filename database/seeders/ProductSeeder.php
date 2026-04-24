<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's products.
     */
    public function run(): void
    {
        $categories = Category::query()
            ->get()
            ->keyBy('name');

        $products = [
            [
                'category' => 'Minuman',
                'name' => 'Es Teh',
                'price' => 5000,
                'image_path' => 'images/products/es-teh.svg',
                'track_stock' => false,
                'stock_quantity' => null,
                'stock_alert_threshold' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'Minuman',
                'name' => 'Es Jeruk',
                'price' => 7000,
                'image_path' => 'images/products/es-jeruk.svg',
                'track_stock' => false,
                'stock_quantity' => null,
                'stock_alert_threshold' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'Makanan',
                'name' => 'Pentol Bakar',
                'price' => 12000,
                'image_path' => 'images/products/pentol-bakar.svg',
                'track_stock' => true,
                'stock_quantity' => 12,
                'stock_alert_threshold' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'Makanan',
                'name' => 'Sosis Bakar',
                'price' => 10000,
                'image_path' => 'images/products/sosis-bakar.svg',
                'track_stock' => true,
                'stock_quantity' => 10,
                'stock_alert_threshold' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'Snack',
                'name' => 'Keripik Kentang',
                'price' => 8000,
                'image_path' => 'images/products/keripik-kentang.svg',
                'track_stock' => true,
                'stock_quantity' => 14,
                'stock_alert_threshold' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            $category = $categories->get($product['category']);

            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['name' => $product['name']],
                [
                    'category_id' => $category->id,
                    'price' => $product['price'],
                    'image_path' => $product['image_path'],
                    'track_stock' => $product['track_stock'],
                    'stock_quantity' => $product['stock_quantity'],
                    'stock_alert_threshold' => $product['stock_alert_threshold'],
                    'is_active' => $product['is_active'],
                ],
            );
        }
    }
}
