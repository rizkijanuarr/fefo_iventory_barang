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

        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->string('barang_keluar_number')->unique();
            $table->string('barang_keluar_name')->nullable();
            $table->date('date_sold');
            $table->bigInteger('discount')->nullable();
            $table->bigInteger('total');
            $table->bigInteger('profit')->nullable();
            $table->string('payment_method');
            $table->string('status')->nullable();
            $table->boolean('is_returned');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
