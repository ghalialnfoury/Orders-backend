<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'ghalialnfoury@gmail.com',
                'phone' => '0504795417',
                'password' => bcrypt('123456'),
                'role' => 'admin'
            ]
        ]);
    }
}
