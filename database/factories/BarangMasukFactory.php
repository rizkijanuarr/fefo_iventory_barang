<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use App\Models\User;

class BarangMasukFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BarangMasuk::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'barang_id' => Barang::factory(),
            'supplier_id' => Supplier::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'reason' => $this->faker->word(),
            'expiration_date' => $this->faker->date(),
            'date_received' => $this->faker->date(),
        ];
    }
}
