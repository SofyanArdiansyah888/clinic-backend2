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
        Schema::table('penjualans', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['pasien_id']);
            
            // Make pasien_id nullable
            $table->unsignedBigInteger('pasien_id')->nullable()->change();
            
            // Re-add foreign key with nullable support
            $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['pasien_id']);
            
            // Make pasien_id not nullable again
            $table->unsignedBigInteger('pasien_id')->nullable(false)->change();
            
            // Re-add foreign key
            $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
        });
    }
};

