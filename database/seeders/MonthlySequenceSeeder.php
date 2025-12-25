<?php

namespace Database\Seeders;

use App\Models\MonthlySequence;
use Illuminate\Database\Seeder;

class MonthlySequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed initial monthly sequences for common models
        $models = ['PAS', 'ANT', 'TRT', 'BRG', 'APT', 'KVS', 'PRD', 'STO', 'KST', 'PRM', 'MEM', 'CAB', 'BNK', 'PRS'];
        $currentYear = date('y');
        $currentMonth = date('m');
        
        foreach ($models as $model) {
            MonthlySequence::create([
                'model' => $model,
                'year_month' => $currentYear . $currentMonth,
                'counter' => 0,
            ]);
        }
    }
}
