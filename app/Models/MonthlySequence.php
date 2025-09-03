<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySequence extends Model
{
    use HasFactory;

    protected $table = 'monthly_sequences';

    protected $fillable = [
        'model',
        'year_month',
        'counter',
    ];

    protected $casts = [
        'counter' => 'integer',
    ];
}
