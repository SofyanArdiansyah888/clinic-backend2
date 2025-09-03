<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'nilai' => 'decimal:2',
        'min_pembelian' => 'decimal:2',
        'max_penggunaan' => 'integer',
        'penggunaan_aktual' => 'integer',
        'is_active' => 'boolean',
    ];
}
