<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->string('barang_masuk_number')->unique();
            $table->string('barang_masuk_name')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('total');
            $table->string('payment_method');
            $table->date('expiration_date');
            $table->date('date_received');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
