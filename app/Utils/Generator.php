<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Generator
{
    /**
     * Mapping model key to table name
     */
    private static $modelTableMap = [
        'PAS' => 'pasiens',
        'ANT' => 'antrians',
        'TRT' => 'treatments',
        'BRG' => 'barangs',
        'APT' => 'appointments',
        'KVS' => 'konversi_stoks',
        'PRB' => 'produksi_barangs',
        'STO' => 'stok_opnames',
        'KST' => 'kartu_stoks',
        'PRM' => 'promos',
        'MEM' => 'memberships',
        'CAB' => 'cabangs',
        'BNK' => 'banks',
        'PRS' => 'perusahaans',
        'SUP' => 'suppliers',
        'STF' => 'staffs',
        'PBL' => 'pembelians',
        'PBD' => 'pembelian_details',
        'PJL' => 'penjualans',
        'PJD' => 'penjualan_details',
        'STD' => 'stok_opname_details',
        'KVD' => 'konversi_stok_details',
        'PRD' => 'produksi_barang_details',
    ];

    /**
     * Generate unique ID for models based on table count
     *
     * @param string $model
     * @return string
     */
    public static function generateID(string $model): string
    {
        $now = Carbon::now();
        $year = $now->format('y');
        $month = $now->format('m');
        
        // Get table name from model key
        $tableName = self::$modelTableMap[$model] ?? null;
        
        if (!$tableName) {
            throw new \InvalidArgumentException("Invalid model key: {$model}");
        }

        // Models that use 'kode' instead of 'id'
        $modelsWithKode = ['PAS', 'ANT', 'STF', 'BNK', 'SUP', 'TRT', 'BRG', 'PBL', 'PBD', 'KST', 'PJL', 'PJD', 'STD', 'KVS', 'KVD', 'PRB', 'PRD'];
        
        if (in_array($model, $modelsWithKode)) {
            // Get count of records for current year-month using kode column
            $count = DB::table($tableName)
                ->whereRaw("SUBSTRING(kode, " . (strlen($model) + 2) . ", 4) = ?", [$year . $month])
                ->count();
        } else {
            // Get count of records for current year-month using id column
            $count = DB::table($tableName)
                ->whereRaw("SUBSTRING(id, " . (strlen($model) + 2) . ", 4) = ?", [$year . $month])
                ->count();
        }

        // Increment counter
        $counter = $count + 1;

        return sprintf('%s-%s%s%05d', $model, $year, $month, $counter);
    }

    /**
     * Get available model keys
     *
     * @return array
     */
    public static function getAvailableKeys(): array
    {
        return [
            'PAS' => 'Pasien',
            'ANT' => 'Antrian',
            'TRT' => 'Treatment',
            'BRG' => 'Barang',
            'APT' => 'Appointment',
            'KVS' => 'Konversi Stok',
            'PRB' => 'Produksi Barang',
            'STO' => 'Stok Opname',
            'KST' => 'Kartu Stok',
            'PRM' => 'Promo',
            'MEM' => 'Membership',
            'CAB' => 'Cabang',
            'BNK' => 'Bank',
            'PRS' => 'Perusahaan',
            'SUP' => 'Supplier',
            'STF' => 'Staff',
            'PBL' => 'Pembelian',
            'PBD' => 'Pembelian Detail',
            'PJL' => 'Penjualan',
            'PJD' => 'Penjualan Detail',
            'STD' => 'Stok Opname Detail',
            'KVD' => 'Konversi Stok Detail',
            'PRD' => 'Produksi Barang Detail'
        ];
    }
}
