<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'kartu_stoks';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'qty_masuk' => 'integer',
        'qty_keluar' => 'integer',
        'saldo' => 'integer',
        'is_active' => 'boolean',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
