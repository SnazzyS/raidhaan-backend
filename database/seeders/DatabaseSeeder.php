<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::updateOrCreate(
            ['email' => 'suhail.1994@hotmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('ThatSnazzy7552'),
                'role' => 'admin',
            ]
        );

        // Create staff user
        User::firstOrCreate(
            ['email' => 'staff@raidhaan.com'],
            [
                'name' => 'Staff',
                'password' => Hash::make('6917'),
                'role' => 'staff',
            ]
        );

        $this->call([
            CategoryTableSeeder::class,
            ItemTableSeeder::class,
            CustomerTableSeeder::class,

        ]);
    }
}
