<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'produksi_barang_details';

    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'is_active' => 'boolean',
    ];

    public function produksiBarang()
    {
        return $this->belongsTo(ProduksiBarang::class, 'produksi_barang_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
