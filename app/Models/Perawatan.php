<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{
    use HasFactory;

    protected $table = 'perawatans';

    protected $fillable = [
        'kode',
        'pasien_id',
        'treatment_id',
        'staff_id',
        'antrian_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'diagnosa',
        'tindakan',
        'catatan',
        'biaya',
        'anamnesis',
        'pemeriksaan_awal',
        'pemeriksaan',
        'kunjungan_berikutnya',
        'foto_perawatan',
        'foto_sebelum',
        'foto_sesudah',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'kunjungan_berikutnya' => 'date',
        // 'jam_mulai' and 'jam_selesai' are TIME fields - handled as strings, Laravel doesn't have a 'time' cast
        'biaya' => 'decimal:2',
        'foto_perawatan' => 'array',
        'foto_sebelum' => 'array',
        'foto_sesudah' => 'array',
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

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    public function reseps()
    {
        return $this->hasMany(PerawatanResep::class);
    }

    public function tindakans()
    {
        return $this->hasMany(PerawatanTindakan::class);
    }
}
