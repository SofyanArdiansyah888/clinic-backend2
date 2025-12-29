<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatanResepBarang extends Model
{
    use HasFactory;

    protected $table = 'perawatan_resep_barang';

    protected $fillable = [
        'perawatan_resep_id',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'jumlah',
        'unit',
        'harga',
        'total',
        'is_active',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga' => 'decimal:2',
        'total' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function perawatanResep()
    {
        return $this->belongsTo(PerawatanResep::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

