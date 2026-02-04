<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

public function run(): void
{
    DB::table('users')->insert([
        [
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('123456'),
        ]
    ]);

    DB::table('restaurants')->insert([
        [
            'name' => 'Al Sham Restaurant',
            'description' => 'Best Syrian food',
        ]
    ]);

    DB::table('products')->insert([
        [
            'name' => 'Shawarma',
            'price' => 25,
            'restaurant_id' => 1
        ],
        [
            'name' => 'Kebab',
            'price' => 40,
            'restaurant_id' => 1
        ]
    ]);
}

