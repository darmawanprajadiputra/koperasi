<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_categories' => 'Bahan pokok',    'slug' => 'bahan-pokok'],
            ['name_categories' => 'Buah-buahan',   'slug' => 'buah-buahan'],
            ['name_categories' => 'Sayuran',   'slug' => 'sayuran'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insertOrIgnore($category);
        }
    }
}