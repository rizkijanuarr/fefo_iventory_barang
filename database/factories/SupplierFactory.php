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
        $fakerId = Faker::create('id_ID'); // Locale Indonesia
        $fakerEn = Faker::create('en_US'); // Locale Inggris

        // Generate random email mixing both locales
        $namePart = $fakerId->firstName(); // Nama depan dari locale Indonesia
        $domainPart = $fakerEn->domainName(); // Domain dari locale Inggris

        return [
            'name' => $fakerId->name(), // Nama orang Indonesia
            'email' => strtolower($namePart) . '@' . $domainPart, // Campur nama dan domain
            'phone_number' => '+62' . $fakerId->numerify('##########'), // Nomor telepon Indonesia
            'address' => $fakerId->address(), // Alamat Indonesia
        ];
    }
}
