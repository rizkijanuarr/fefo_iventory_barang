<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1
        \App\Models\User::create([
            'name' => 'Tria Icha',
            'email'  => 'tria@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // 2
        \App\Models\User::create([
            'name' => 'Naila',
            'email'  => 'naila@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // 3
        \App\Models\User::create([
            'name' => 'Nabila',
            'email'  => 'nabila@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
