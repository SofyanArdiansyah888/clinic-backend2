<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nilai' => 'decimal:2',
        'minimal_pembelian' => 'decimal:2',
        'maksimal_diskon' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function isActive()
    {
        $now = now();
        return $this->is_active && 
               $this->tanggal_mulai <= $now && 
               $this->tanggal_selesai >= $now;
    }

    public function calculateDiscount($subtotal)
    {
        if (!$this->isActive() || $subtotal < $this->minimal_pembelian) {
            return 0;
        }

        if ($this->jenis === 'percentage') {
            $discount = $subtotal * ($this->nilai / 100);
        } else {
            $discount = $this->nilai;
        }

        if ($this->maksimal_diskon > 0) {
            $discount = min($discount, $this->maksimal_diskon);
        }

        return $discount;
    }
}
