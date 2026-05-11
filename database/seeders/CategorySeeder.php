<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_categories' => 'Dairy (Produk Susu)',    'slug' => 'dairy'],
            ['name_categories' => 'Fruits (Buah-buahan)',   'slug' => 'fruits'],
            ['name_categories' => 'Vegetables (Sayuran)',   'slug' => 'vegetables'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insertOrIgnore($category);
        }
    }
}