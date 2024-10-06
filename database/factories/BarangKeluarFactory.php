<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarangKeluarFactory extends Factory
{
    protected $model = \App\Models\BarangKeluar::class;

    public function definition()
    {
        return [
            'user_id' => 1, // Adjust as needed
            'customer_id' => rand(1, 50),
            'barang_id' => rand(1, 50),
            'barang_keluar_number' =>
            $this->faker->unique()->bothify('BKN-########'),
            'barang_keluar_name' => ucfirst($this->faker->word),
            'date_sold' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'discount' => $this->faker->numberBetween(5000, 10000),
            'total' => 0,
            'payment_method' => collect(\App\Enums\PaymentMethod::cases())->random(),
            'status' => collect(\App\Enums\Status::cases())->random(),
            'is_returned' => $this->faker->boolean(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (\App\Models\BarangKeluar $barang_keluar) {})->afterCreating(function (\App\Models\BarangKeluar $barang_keluar) {
            $barangIds = \App\Models\Barang::query()->inRandomOrder()->take(rand(1, 5))->pluck('id');
            $barangKeluarDetails = $barangIds->map(function ($barangId) use ($barang_keluar) {
                $quantity = rand(1, 10);
                $price = \App\Models\Barang::find($barangId)->price;
                $subtotal = $quantity * $price;

                return [
                    'barang_keluar_id' => $barang_keluar->id,
                    'barang_id' => $barangId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            });

            // Insert BarangKeluarDetails
            \App\Models\BarangKeluarDetail::insert($barangKeluarDetails->toArray());


            $total = $barangKeluarDetails->sum('subtotal') - $barang_keluar->discount;

            $barang_keluar->total = $total;
            $barang_keluar->profit = $total * 0.1;
            $barang_keluar->save();
        });
    }
}
