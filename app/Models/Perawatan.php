<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{
    use HasFactory;

    protected $table = 'perawatans';

    protected $fillable = [
        'id',
        'pasien_id',
        'treatment_id',
        'staff_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'diagnosa',
        'tindakan',
        'catatan',
        'biaya',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'time',
        'jam_selesai' => 'time',
        'biaya' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
