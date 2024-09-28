<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Supplier;
use Faker\Factory as Faker;


class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $faker = Faker::create('id_ID');

        return [
            'name' => 'PT. ' . $faker->company(), // Nama orang Indonesia
            'email' => $faker->unique()->safeEmail(),
            'phone_number' => '+62' . $faker->numerify('##########'), // Nomor telepon Indonesia
            'address' => $faker->address(), // Alamat Indonesia
        ];
    }
}
