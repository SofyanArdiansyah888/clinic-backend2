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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('deskripsi');
            $table->enum('jenis', ['percentage', 'fixed']);
            $table->decimal('nilai', 10, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('minimal_pembelian', 10, 2);
            $table->decimal('maksimal_diskon', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
