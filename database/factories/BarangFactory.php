<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Barang;


class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition()
    {
        return [
            'category_id' => rand(1, 10),
            'name' => $this->faker->word,
            'sku' => $this->faker->unique()->numerify('SKU-#####'),
            'description' => $this->faker->paragraph(true),
            'stock_quantity' => 0,
            'cost_price' => $costPrice = $this->faker->numberBetween(10000, 100000),
            'price' => $costPrice + ($costPrice * (20 / 100)),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Barang $barang) {
            // Buat BarangMasuk untuk mengisi stok
            $barangMasuk = \App\Models\BarangMasuk::factory()->create([
                'barang_id' => $barang->id,
                'quantity' => $this->faker->numberBetween(1, 1000),
            ]);

            // Update stok Barang
            $barang->update([
                'stock_quantity' => $barangMasuk->quantity,
            ]);
        });
    }
}
