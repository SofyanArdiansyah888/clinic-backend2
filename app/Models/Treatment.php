<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $table = 'treatments';

    protected $fillable = [
        'id',
        'nama',
        'deskripsi',
        'durasi',
        'harga',
        'kategori',
        'is_active',
    ];

    protected $casts = [
        'durasi' => 'integer',
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function appointmentTreatments()
    {
        return $this->hasMany(AppointmentTreatment::class);
    }

    public function perawatans()
    {
        return $this->hasMany(Perawatan::class);
    }
}
