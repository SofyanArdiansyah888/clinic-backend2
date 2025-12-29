<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatanResep extends Model
{
    use HasFactory;

    protected $table = 'perawatan_resep';

    protected $fillable = [
        'perawatan_id',
        'antrian_id',
        'pasien_id',
        'staff_id',
        'kode',
        'tanggal',
        'status',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'tanggal' => null,
        'status' => 'draft',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tanggal)) {
                $model->tanggal = now();
            }
            if (empty($model->status)) {
                $model->status = 'draft';
            }
        });
    }

    public function perawatan()
    {
        return $this->belongsTo(Perawatan::class);
    }

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function barangs()
    {
        return $this->hasMany(PerawatanResepBarang::class);
    }
}

