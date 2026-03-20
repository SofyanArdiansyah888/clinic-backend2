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
        if (Schema::hasColumn('banks', 'no_bank') && !Schema::hasColumn('banks', 'kode')) {
            Schema::table('banks', function (Blueprint $table) {
                $table->renameColumn('no_bank', 'kode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('banks', 'kode') && !Schema::hasColumn('banks', 'no_bank')) {
            Schema::table('banks', function (Blueprint $table) {
                $table->renameColumn('kode', 'no_bank');
            });
        }
    }
};
