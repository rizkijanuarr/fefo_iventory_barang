<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    protected $model = \App\Models\Barang::class;

    public function definition()
    {
        $costPrice = $this->faker->numberBetween(10000, 100000); // Harga pokok
        $price = $costPrice + ($costPrice * (20 / 100)); // Harga jual (20% markup)

        return [
            'category_id' => rand(1, 10),
            'name' => $this->faker->word,
            'barcode' => $this->faker->unique()->numerify('BR-#####'),
            'description' => $this->faker->paragraph(true),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'cost_price' => $costPrice,
            'price' => $price,
            'expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
        ];
    }
}
