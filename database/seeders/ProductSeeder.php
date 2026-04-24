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
        $legacyDemoNames = [
            'Es Teh',
            'Es Jeruk',
            'Pentol Bakar',
            'Sosis Bakar',
            'Keripik Kentang',
        ];

        $products = [
            ['category' => 'Makanan', 'name' => 'Taso Goreng', 'menu_group' => 'Cemilan', 'price' => 10000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Bakso Goreng', 'menu_group' => 'Bakso', 'price' => 10000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Sempol Ayam', 'menu_group' => 'Sempol', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 18, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Tahu Jontor', 'menu_group' => 'Tahu', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 16, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Tahu Jontor Satuan', 'menu_group' => 'Tahu', 'price' => 3000, 'selling_unit' => 'Satuan', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 25, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Singkong Goreng', 'menu_group' => 'Cemilan', 'price' => 10000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/keripik-kentang.svg', 'track_stock' => true, 'stock_quantity' => 14, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Aneka Mie Goreng', 'menu_group' => 'Mie', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 15, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Roti Bakar Aneka Rasa', 'menu_group' => 'Roti', 'price' => 15000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 12, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Pisang Goreng Original', 'menu_group' => 'Pisang', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 18, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Pisang Keju', 'menu_group' => 'Pisang', 'price' => 15000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 18, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Pisang Cokelat', 'menu_group' => 'Pisang', 'price' => 15000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 18, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Pisang Cokelat Keju', 'menu_group' => 'Pisang', 'price' => 17000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 18, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Pisang Mix', 'menu_group' => 'Pisang', 'price' => 18000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 16, 'stock_alert_threshold' => 4, 'is_active' => true],
            ['category' => 'Snack', 'name' => 'Pisang Goreng Satuan', 'menu_group' => 'Pisang', 'price' => 3000, 'selling_unit' => 'Satuan', 'image_path' => 'images/products/placeholder.svg', 'track_stock' => true, 'stock_quantity' => 30, 'stock_alert_threshold' => 6, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Teh Ori', 'menu_group' => 'Es', 'price' => 5000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Teh Susu', 'menu_group' => 'Es', 'price' => 7000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Teh Hijau Ori', 'menu_group' => 'Es', 'price' => 6000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Teh Hijau Susu', 'menu_group' => 'Es', 'price' => 8000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Jeruk Ori', 'menu_group' => 'Es', 'price' => 7000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-jeruk.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Jeruk Susu', 'menu_group' => 'Es', 'price' => 9000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-jeruk.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Milo', 'menu_group' => 'Es', 'price' => 10000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-jeruk.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Drink Beng Beng', 'menu_group' => 'Es', 'price' => 10000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-jeruk.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Chocolate Aneka Rasa', 'menu_group' => 'Es', 'price' => 10000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-jeruk.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Torabika Cappucino', 'menu_group' => 'Es', 'price' => 9000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Goodday Cappucino', 'menu_group' => 'Es', 'price' => 9000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Minuman', 'name' => 'Es Maxtea Tarik', 'menu_group' => 'Es', 'price' => 9000, 'selling_unit' => 'Gelas', 'image_path' => 'images/products/es-teh.svg', 'track_stock' => false, 'stock_quantity' => null, 'stock_alert_threshold' => 3, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Telur Puyuh', 'menu_group' => 'Pentol', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Mercon', 'menu_group' => 'Pentol', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Keju', 'menu_group' => 'Pentol', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Urat', 'menu_group' => 'Pentol', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Jamur', 'menu_group' => 'Pentol', 'price' => 12000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 20, 'stock_alert_threshold' => 5, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Kecil', 'menu_group' => 'Pentol', 'price' => 10000, 'selling_unit' => 'Porsi', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 24, 'stock_alert_threshold' => 6, 'is_active' => true],
            ['category' => 'Makanan', 'name' => 'Pentol Kecil Satuan', 'menu_group' => 'Pentol', 'price' => 2500, 'selling_unit' => 'Satuan', 'image_path' => 'images/products/pentol-bakar.svg', 'track_stock' => true, 'stock_quantity' => 40, 'stock_alert_threshold' => 8, 'is_active' => true],
        ];

        Product::query()
            ->whereIn('name', $legacyDemoNames)
            ->update(['is_active' => false]);

        foreach ($products as $product) {
            $category = $categories->get($product['category']);

            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['name' => $product['name']],
                [
                    'category_id' => $category->id,
                    'menu_group' => $product['menu_group'],
                    'price' => $product['price'],
                    'selling_unit' => $product['selling_unit'],
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
