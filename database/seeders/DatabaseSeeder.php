<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
        ]);

        \App\Models\Supplier::factory(50)->create();
        \App\Models\Customer::factory(50)->create();
        // \App\Models\Barang::factory(50)->create();
        // \App\Models\BarangMasuk::factory(50)->create();
        // \App\Models\BarangKeluar::factory(50)->create();
    }
}
