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
        Schema::create('produksi_barang_details', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('produksi_barang_id');
            $table->string('barang_id');
            $table->integer('qty');
            $table->enum('tipe', ['input', 'output']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('produksi_barang_id')->references('id')->on('produksi_barangs')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_barang_details');
    }
};
