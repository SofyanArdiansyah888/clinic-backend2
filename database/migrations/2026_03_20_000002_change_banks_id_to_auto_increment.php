<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** 
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('banks') || !Schema::hasColumn('banks', 'id')) {
            return;
        }

        Schema::create('banks_temp_auto_id', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_bank');
            $table->enum('jenis_bank', ['bank', 'e-money']);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->string('no_rekening');
            $table->string('atas_nama');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('banks_temp_auto_id')->insertUsing(
            ['kode', 'nama_bank', 'jenis_bank', 'saldo_awal', 'no_rekening', 'atas_nama', 'is_active', 'created_at', 'updated_at'],
            DB::table('banks')->select(
                'kode',
                'nama_bank',
                'jenis_bank',
                'saldo_awal',
                'no_rekening',
                'atas_nama',
                'is_active',
                'created_at',
                'updated_at'
            )
        );

        Schema::drop('banks');
        Schema::rename('banks_temp_auto_id', 'banks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('banks')) {
            return;
        }

        Schema::create('banks_temp_string_id', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('kode')->unique();
            $table->string('nama_bank');
            $table->enum('jenis_bank', ['bank', 'e-money']);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->string('no_rekening');
            $table->string('atas_nama');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $banks = DB::table('banks')->select(
            'id',
            'kode',
            'nama_bank',
            'jenis_bank',
            'saldo_awal',
            'no_rekening',
            'atas_nama',
            'is_active',
            'created_at',
            'updated_at'
        )->get();

        foreach ($banks as $bank) {
            DB::table('banks_temp_string_id')->insert([
                'id' => 'BNK' . str_pad((string) $bank->id, 6, '0', STR_PAD_LEFT),
                'kode' => $bank->kode,
                'nama_bank' => $bank->nama_bank,
                'jenis_bank' => $bank->jenis_bank,
                'saldo_awal' => $bank->saldo_awal,
                'no_rekening' => $bank->no_rekening,
                'atas_nama' => $bank->atas_nama,
                'is_active' => $bank->is_active,
                'created_at' => $bank->created_at,
                'updated_at' => $bank->updated_at,
            ]);
        }

        Schema::drop('banks');
        Schema::rename('banks_temp_string_id', 'banks');
    }
};
