<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'lokasi_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok_minimal',
        'stok_aktual',
        'is_active',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok_minimal' => 'integer',
        'stok_aktual' => 'integer',
        'is_active' => 'boolean',
    ];

    public function stokMovements()
    {
        return $this->hasMany(StokMovement::class);
    }

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
