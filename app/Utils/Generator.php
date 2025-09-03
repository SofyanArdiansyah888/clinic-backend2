<?php

namespace App\Utils;

use App\Models\MonthlySequence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Generator
{
    /**
     * Generate unique ID for models
     *
     * @param string $model
     * @param bool $update
     * @return string
     */
    public static function generateID(string $model, bool $update = true): string
    {
        $now = Carbon::now();
        $year = $now->format('y');
        $month = $now->format('m');
        $yearMonth = $year . $month;

        $seq = MonthlySequence::where('model', $model)
            ->where('year_month', $yearMonth)
            ->first();

        if (!$seq) {
            $seq = new MonthlySequence([
                'model' => $model,
                'year_month' => $yearMonth,
                'counter' => 1,
            ]);
            
            if ($update) {
                $seq->save();
            }
        } else {
            $seq->counter++;
            if ($update) {
                $seq->save();
            }
        }

        return sprintf('%s-%s%s%05d', $model, $year, $month, $seq->counter);
    }
}
