<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->insert([
                [
                'phone_number' => 7999065,
                'address' => 'Vinares Tower 9B',
                'city' => 'Hulhumale Phase 1'
                ]
                ]);
    }
}
