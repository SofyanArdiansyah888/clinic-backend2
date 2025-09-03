<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'id',
        'nama',
        'nip',
        'jabatan',
        'departemen',
        'no_telp',
        'email',
        'alamat',
        'tanggal_bergabung',
        'is_active',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
