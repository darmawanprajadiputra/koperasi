<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name_unit' => 'KG',    'slug' => 'kg'],
            ['name_unit' => 'DUS',   'slug' => 'dus'],
            ['name_unit' => 'PCS',   'slug' => 'pcs'],
            ['name_unit' => 'KRAT', 'slug' => 'krat'],
            ['name_unit' => 'PACK', 'slug' => 'pack'],
            ['name_unit' => 'KARTON', 'slug' => 'karton'],
            ['name_unit' => 'KARUNG', 'slug' => 'karung'],
            ['name_unit' => 'BUTIR', 'slug' => 'butir'],
            ['name_unit' => 'LITER', 'slug' => 'liter'],
            ['name_unit' => 'BOX', 'slug' => 'box'],
        ];

        foreach ($units as $unit) {
            DB::table('unit')->insertOrIgnore($unit);
        }
    }
}