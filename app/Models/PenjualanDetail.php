<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_details';

    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'harga_jual' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
