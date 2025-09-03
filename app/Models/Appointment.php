<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'id',
        'pasien_id',
        'staff_id',
        'tanggal',
        'jam',
        'status',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'time',
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

    public function treatments()
    {
        return $this->hasMany(AppointmentTreatment::class);
    }
}
