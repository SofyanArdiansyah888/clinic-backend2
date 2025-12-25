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
        Schema::create('banks', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('no_bank')->unique();
            $table->string('nama_bank');
            $table->enum('jenis_bank', ['bank', 'e-money']);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->string('no_rekening');
            $table->string('atas_nama');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
