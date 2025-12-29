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
        Schema::create('perawatan_tindakan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perawatan_id');
            $table->unsignedBigInteger('treatment_id');
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->unsignedBigInteger('beautician_id')->nullable();
            $table->decimal('harga', 10, 2);
            $table->decimal('diskon', 10, 2)->nullable();
            $table->decimal('rp_percent', 5, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('status', ['draft', 'confirmed', 'completed', 'cancelled'])->default('draft');
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('perawatan_id')->references('id')->on('perawatans')->onDelete('cascade');
            $table->foreign('treatment_id')->references('id')->on('treatments')->onDelete('cascade');
            $table->foreign('beautician_id')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perawatan_tindakan');
    }
};

