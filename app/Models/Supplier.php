<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'id',
        'nama',
        'alamat',
        'no_telp',
        'email',
        'contact_person',
        'npwp',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}
