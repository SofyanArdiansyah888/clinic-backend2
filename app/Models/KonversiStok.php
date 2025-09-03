<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonversiStok extends Model
{
    use HasFactory;

    protected $table = 'konversi_stoks';
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
        return $this->hasMany(KonversiStokDetail::class, 'konversi_stok_id');
    }
}
