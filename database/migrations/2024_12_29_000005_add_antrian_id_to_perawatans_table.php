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
            $table->unsignedBigInteger('antrian_id')->nullable()->after('staff_id');
            $table->foreign('antrian_id')->references('id')->on('antrians')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perawatans', function (Blueprint $table) {
            $table->dropForeign(['antrian_id']);
            $table->dropColumn('antrian_id');
        });
    }
};

