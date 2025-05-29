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
    

        User::create([
          'name' => 'Admin',
          'email' => 'admin@raidhaan.com',
          'password' => Hash::make('That7552'),
          'role' => 'admin',
        ]);

        // Create staff user
        User::create([
            'name' => 'Staff',
            'email' => 'staff@raidhaan.com',
            'password' => Hash::make('6917'),
            'role' => 'staff',
        ]);

        $this->call([
            CategoryTableSeeder::class,
            ItemTableSeeder::class,
            CustomerTableSeeder::class,
          
        ]);
    }
}
