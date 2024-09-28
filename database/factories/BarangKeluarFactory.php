<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Customer;
use App\Models\User;

class BarangKeluarFactory extends Factory
{
    protected $model = BarangKeluar::class;

    public function definition()
    {
        return [
            'user_id' => null, // Adjust as needed
            'customer_id' => Customer::factory(),
            'barang_id' => Barang::factory(),
            'quantity_adjusted' => $this->faker->numberBetween(1, 50),
            'reason' => $this->faker->sentence,
            'date_sold' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (BarangKeluar $barangKeluar) {
            $barang = Barang::find($barangKeluar->barang_id);
            $barang->update([
                'stock_quantity' => $barang->stock_quantity - $barangKeluar->quantity_adjusted,
            ]);
        });
    }
}
