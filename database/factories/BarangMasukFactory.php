<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarangMasukFactory extends Factory
{
    protected $model = \App\Models\BarangMasuk::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'barang_id' => rand(1, 50),
            'supplier_id' => rand(1, 50),
            'barang_masuk_number' =>
            $this->faker->unique()->bothify('BMN-########'),
            'quantity' => $this->faker->numberBetween(1, 50),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'date_received' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'total' => 0,
            'payment_method' => collect(\App\Enums\PaymentMethod::cases())->random(),
            'status' => collect(\App\Enums\Status::cases())->random(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (\App\Models\BarangMasuk $barang_masuk) {})->afterCreating(function (\App\Models\BarangMasuk $barang_masuk) {
            $barangIds = \App\Models\Barang::query()->inRandomOrder()->take(rand(1, 5))->pluck('id');
            $barangMasukDetails = $barangIds->map(function ($barangId) use ($barang_masuk) {
                $quantity = rand(1, 10);
                $price = \App\Models\Barang::find($barangId)->price;
                $subtotal = $quantity * $price;

                return [
                    'barang_masuk_id' => $barang_masuk->id,
                    'barang_id' => $barangId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            });

            // Insert BarangMasukDetails
            \App\Models\BarangMasukDetail::insert($barangMasukDetails->toArray());

            // Menghitung total tanpa diskon atau profit
            $total = $barangMasukDetails->sum('subtotal');

            // Update total ke barang_masuk
            $barang_masuk->total = $total;
            $barang_masuk->save();
        });
    }
}
