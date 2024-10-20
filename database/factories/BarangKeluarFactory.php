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
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BarangKeluar::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'barang_id' => Barang::factory(),
            'customer_id' => Customer::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'reason' => $this->faker->word(),
            'date_sold' => $this->faker->date(),
        ];
    }
}
