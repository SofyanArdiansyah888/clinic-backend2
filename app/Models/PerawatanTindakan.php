<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatanTindakan extends Model
{
    use HasFactory;

    protected $table = 'perawatan_tindakan';

    protected $fillable = [
        'perawatan_id',
        'treatment_id',
        'tanggal',
        'jumlah',
        'beautician_id',
        'harga',
        'diskon',
        'rp_percent',
        'total',
        'status',
        'catatan',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'rp_percent' => 'decimal:2',
        'total' => 'decimal:2',
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

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function beautician()
    {
        return $this->belongsTo(Staff::class, 'beautician_id');
    }
}

