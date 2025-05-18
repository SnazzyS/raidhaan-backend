<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            ['name' => 'Mandi', 'price' => 65, 'category_id' => 1],
            ['name' => 'Kabsa Large', 'price' => 120, 'category_id' => 1],
            ['name' => 'Orange Juice', 'price' => 40, 'category_id' => 2],


        ]);
    }
}
