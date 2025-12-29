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
        Schema::table('perawatans', function (Blueprint $table) {
            $table->text('anamnesis')->nullable()->after('catatan');
            $table->text('pemeriksaan_awal')->nullable()->after('anamnesis');
            $table->text('pemeriksaan')->nullable()->after('pemeriksaan_awal');
            $table->date('kunjungan_berikutnya')->nullable()->after('pemeriksaan');
            $table->json('foto_perawatan')->nullable()->after('kunjungan_berikutnya');
            $table->json('foto_sebelum')->nullable()->after('foto_perawatan');
            $table->json('foto_sesudah')->nullable()->after('foto_sebelum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perawatans', function (Blueprint $table) {
            $table->dropColumn([
                'anamnesis',
                'pemeriksaan_awal',
                'pemeriksaan',
                'kunjungan_berikutnya',
                'foto_perawatan',
                'foto_sebelum',
                'foto_sesudah'
            ]);
        });
    }
};

