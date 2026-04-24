<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Minuman',
                'description' => 'Menu minuman booth.',
            ],
            [
                'name' => 'Makanan',
                'description' => 'Menu makanan booth.',
            ],
            [
                'name' => 'Snack',
                'description' => 'Menu camilan booth.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']],
            );
        }
    }
}
