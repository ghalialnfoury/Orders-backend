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
            'email' => 'ghalialnfoury@gmail.com',
             "phone": "0504795417",
            'password' => bcrypt('123456'),
            "role": "Admin"
        ]
    ]);

   
