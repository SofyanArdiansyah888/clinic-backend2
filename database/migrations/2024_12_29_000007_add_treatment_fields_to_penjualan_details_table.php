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
        Schema::table('penjualan_details', function (Blueprint $table) {
            // Make barang_id nullable
            $table->unsignedBigInteger('barang_id')->nullable()->change();
            
            // Add treatment_id (nullable)
            $table->unsignedBigInteger('treatment_id')->nullable()->after('barang_id');
            
            // Add jenis_penjualan enum
            $table->enum('jenis_penjualan', ['barang', 'treatment'])->default('barang')->after('treatment_id');
            
            // Add foreign key for treatment_id
            $table->foreign('treatment_id')->references('id')->on('treatments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_details', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['treatment_id']);
            
            // Drop columns
            $table->dropColumn(['treatment_id', 'jenis_penjualan']);
            
            // Revert barang_id to not nullable
            $table->unsignedBigInteger('barang_id')->nullable(false)->change();
        });
    }
};

