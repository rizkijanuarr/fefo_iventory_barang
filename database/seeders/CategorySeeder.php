<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'Makanan',
            'Minuman',
            'Peralatan Masak',
            'Kopi',
            'Teh',
            'Camilan',
            'Permen',
            'Roti',
            'Kue',
            'Sarapan',
        ])->each(fn($category) => \App\Models\Category::query()->create(['name' => $category]));
    }
}
