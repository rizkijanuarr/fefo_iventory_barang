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
            $table->foreignId('barang_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->string('batch_number')->unique();
            $table->integer('quantity');
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
