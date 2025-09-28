# Generator Update Documentation

## Perubahan Sistem Penomoran

### Sebelumnya (Menggunakan MonthlySequence)
- Menggunakan tabel `monthly_sequences` terpisah untuk menyimpan counter
- Setiap model memiliki record di tabel `monthly_sequences`
- Counter disimpan dan diupdate setiap kali generate ID

### Sekarang (Menggunakan Counter dari Tabel Masing-masing)
- **Tidak menggunakan tabel `monthly_sequences`**
- Counter dihitung langsung dari jumlah record di tabel masing-masing
- Format ID tetap sama: `{KEY}-{YY}{MM}{NNNNN}`

## Perubahan di Generator.php

### Mapping Model ke Tabel
```php
private static $modelTableMap = [
    'PAS' => 'pasiens',
    'ANT' => 'antrians',
    'TRT' => 'treatments',
    'BRG' => 'barangs',
    'APT' => 'appointments',
    'KVS' => 'konversi_stoks',
    'PRD' => 'produksi_barangs',
    'STO' => 'stok_opnames',
    'KST' => 'kartu_stoks',
    'PRM' => 'promos',
    'MEM' => 'memberships',
    'CAB' => 'cabangs',
    'BNK' => 'banks',
    'PRS' => 'perusahaans',
    'SUP' => 'suppliers',
];
```

### Metode generateID Baru
```php
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

    // Get count of records for current year-month
    $count = DB::table($tableName)
        ->whereRaw("SUBSTRING(id, " . (strlen($model) + 2) . ", 4) = ?", [$year . $month])
        ->count();

    // Increment counter
    $counter = $count + 1;

    return sprintf('%s-%s%s%05d', $model, $year, $month, $counter);
}
```

## Cara Kerja Sistem Baru

### 1. Identifikasi Tabel
- Berdasarkan `$model` (contoh: 'SUP'), sistem akan mencari tabel yang sesuai (`suppliers`)

### 2. Hitung Counter
- Sistem akan menghitung berapa banyak record di tabel `suppliers` yang memiliki ID dengan format tahun-bulan yang sama
- Query: `WHERE SUBSTRING(id, 5, 4) = '2412'` (untuk Desember 2024)

### 3. Generate ID
- Counter = jumlah record + 1
- Format: `SUP-241200001` (jika belum ada record), `SUP-241200002` (jika sudah ada 1 record), dst.

## Keuntungan Sistem Baru

### 1. **Tidak Perlu Tabel Terpisah**
- Tidak memerlukan tabel `monthly_sequences`
- Struktur database lebih sederhana

### 2. **Self-Contained**
- Setiap tabel mengelola counter-nya sendiri
- Tidak ada dependency antar tabel

### 3. **Otomatis Reset Bulanan**
- Counter otomatis reset setiap bulan baru
- Berdasarkan timestamp saat generate ID

### 4. **Thread Safe**
- Menggunakan database query untuk menghitung counter
- Tidak ada race condition

## Contoh Penggunaan

### Generate ID Supplier
```php
$supplierId = Generator::generateID('SUP');
// Hasil: SUP-241200001 (jika belum ada supplier di bulan ini)
// Hasil: SUP-241200002 (jika sudah ada 1 supplier di bulan ini)
```

### Generate ID Pasien
```php
$pasienId = Generator::generateID('PAS');
// Hasil: PAS-241200001 (jika belum ada pasien di bulan ini)
```

## Endpoint API

### Generate Number
```
GET /api/generate-number?key=SUP
```

Response:
```json
{
    "success": true,
    "message": "ID generated successfully",
    "data": {
        "generated_id": "SUP-241200001",
        "key": "SUP",
        "timestamp": "2024-12-01T10:30:00.000000Z"
    }
}
```

## Catatan Penting

### 1. **Format ID Harus Konsisten**
- Semua record di tabel harus menggunakan format ID yang sama
- Jika ada record dengan format ID berbeda, counter mungkin tidak akurat

### 2. **Performance**
- Query `SUBSTRING` dan `COUNT` akan berjalan setiap kali generate ID
- Untuk tabel dengan banyak data, pertimbangkan indexing pada kolom `id`

### 3. **Backup Data**
- Pastikan backup data sebelum menghapus tabel `monthly_sequences` (jika ada)

## Migration dari Sistem Lama

Jika sebelumnya menggunakan `MonthlySequence`, Anda bisa:

1. **Hapus tabel `monthly_sequences`** (jika tidak digunakan lagi)
2. **Update seeder** untuk tidak lagi seed `MonthlySequence`
3. **Test endpoint** untuk memastikan generate ID berfungsi dengan benar

## Testing

Route sudah terdaftar dengan benar:
```
GET|HEAD  api/generate-number ........... GeneratorController@generateNumber
GET|HEAD  api/generate-number/keys .... GeneratorController@getAvailableKeys
```

Endpoint siap digunakan untuk generate ID berdasarkan counter dari tabel masing-masing.
