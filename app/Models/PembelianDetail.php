<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_details';

    protected $fillable = [
        'kode',
        'pembelian_id',
        'barang_id',
        'qty',
        'harga_beli',
        'subtotal',
        'is_active',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
