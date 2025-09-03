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
        Schema::create('konversi_stok_details', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('konversi_stok_id');
            $table->string('barang_id');
            $table->integer('qty');
            $table->enum('tipe', ['input', 'output']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('konversi_stok_id')->references('id')->on('konversi_stoks')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konversi_stok_details');
    }
};
