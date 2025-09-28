<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonversiStokDetail extends Model
{
    use HasFactory;

    protected $table = 'konversi_stok_details';

    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'is_active' => 'boolean',
    ];

    public function konversiStok()
    {
        return $this->belongsTo(KonversiStok::class, 'konversi_stok_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
