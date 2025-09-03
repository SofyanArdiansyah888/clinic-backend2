<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'total_harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
