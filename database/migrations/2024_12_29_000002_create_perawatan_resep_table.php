<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perawatan_resep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perawatan_id');
            $table->unsignedBigInteger('antrian_id');
            $table->unsignedBigInteger('pasien_id');
            $table->unsignedBigInteger('staff_id');
            $table->string('kode')->unique();
            $table->date('tanggal');
            $table->enum('status', ['draft', 'confirmed', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('perawatan_id')->references('id')->on('perawatans')->onDelete('cascade');
            $table->foreign('antrian_id')->references('id')->on('antrians')->onDelete('cascade');
            $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perawatan_resep');
    }
};

