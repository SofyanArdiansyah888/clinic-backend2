<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'memberships';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'poin' => 'integer',
        'is_active' => 'boolean',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function isActive()
    {
        $now = now();
        return $this->is_active && 
               $this->status === 'active' &&
               $this->tanggal_mulai <= $now && 
               $this->tanggal_selesai >= $now;
    }

    public function addPoints($amount)
    {
        $this->poin += $amount;
        $this->save();
    }

    public function usePoints($amount)
    {
        if ($this->poin >= $amount) {
            $this->poin -= $amount;
            $this->save();
            return true;
        }
        return false;
    }
}
