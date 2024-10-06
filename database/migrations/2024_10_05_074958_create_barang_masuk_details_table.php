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

        Schema::create('barang_masuk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')->nullable()->constrained();
            $table->foreignId('barang_id')->nullable()->constrained();
            $table->integer('quantity');
            $table->bigInteger('price');
            $table->bigInteger('subtotal');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_details');
    }
};
