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
        Schema::create('perawatan_resep_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perawatan_resep_id');
            $table->unsignedBigInteger('barang_id');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->decimal('jumlah', 10, 2);
            $table->string('unit');
            $table->decimal('harga', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('perawatan_resep_id')->references('id')->on('perawatan_resep')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perawatan_resep_barang');
    }
};

