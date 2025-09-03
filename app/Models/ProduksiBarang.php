<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiBarang extends Model
{
    use HasFactory;

    protected $table = 'produksi_barangs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(ProduksiBarangDetail::class, 'produksi_barang_id');
    }
}
