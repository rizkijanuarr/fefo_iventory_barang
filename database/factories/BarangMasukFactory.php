<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;

class BarangMasukFactory extends Factory
{
    protected $model = BarangMasuk::class;

    public function definition()
    {
        return [
            'barang_id' => Barang::factory(),
            'supplier_id' => Supplier::factory(),
            'batch_number' => $this->faker->unique()->numerify('BATCH-#####'),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'date_received' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (BarangMasuk $barangMasuk) {
            $barang = Barang::find($barangMasuk->barang_id);
            $barang->update([
                'stock_quantity' => $barang->stock_quantity + $barangMasuk->quantity,
            ]);
        });
    }
}
