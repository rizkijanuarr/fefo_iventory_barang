<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use Faker\Factory as Faker;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $faker = Faker::create('id_ID');
        $faker->unique(true); // Reset unique generator

        return [
            'name' => $faker->name(), // Nama orang Indonesia
            'email' => $faker->unique(true)->safeEmail(),
            'phone_number' => '+62' . $faker->numerify('##########'), // Nomor telepon Indonesia
            'address' => $faker->address(), // Alamat Indonesia
        ];
    }
}
