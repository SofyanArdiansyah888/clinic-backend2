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
        Schema::create('monthly_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('year_month');
            $table->integer('counter')->default(1);
            $table->timestamps();
            
            $table->unique(['model', 'year_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_sequences');
    }
};
