# Generator Endpoint Documentation

## Overview
Endpoint `generate-number` telah dibuat untuk menghasilkan ID unik berdasarkan model yang diminta.

## Endpoint yang Tersedia

### 1. Generate Number
**URL:** `GET /api/generate-number?key={KEY}`

**Parameters:**
- `key` (required): String key untuk model yang akan di-generate ID-nya

**Available Keys:**
- `PAS` - Pasien
- `ANT` - Antrian
- `TRT` - Treatment
- `BRG` - Barang
- `APT` - Appointment
- `KVS` - Konversi Stok
- `PRD` - Produksi Barang
- `STO` - Stok Opname
- `KST` - Kartu Stok
- `PRM` - Promo
- `MEM` - Membership
- `CAB` - Cabang
- `BNK` - Bank
- `PRS` - Perusahaan
- `SUP` - Supplier

**Response Format:**
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

**Error Response:**
```json
{
    "success": false,
    "message": "Parameter key is required",
    "data": null
}
```

### 2. Get Available Keys
**URL:** `GET /api/generate-number/keys`

**Response:**
```json
{
    "success": true,
    "message": "Available keys retrieved successfully",
    "data": {
        "PAS": "Pasien",
        "ANT": "Antrian",
        "TRT": "Treatment",
        "BRG": "Barang",
        "APT": "Appointment",
        "KVS": "Konversi Stok",
        "PRD": "Produksi Barang",
        "STO": "Stok Opname",
        "KST": "Kartu Stok",
        "PRM": "Promo",
        "MEM": "Membership",
        "CAB": "Cabang",
        "BNK": "Bank",
        "PRS": "Perusahaan",
        "SUP": "Supplier"
    }
}
```

## Contoh Penggunaan

### Generate ID untuk Supplier
```bash
curl -X GET "http://localhost:8000/api/generate-number?key=SUP" \
     -H "Accept: application/json"
```

### Generate ID untuk Pasien
```bash
curl -X GET "http://localhost:8000/api/generate-number?key=PAS" \
     -H "Accept: application/json"
```

### Lihat Available Keys
```bash
curl -X GET "http://localhost:8000/api/generate-number/keys" \
     -H "Accept: application/json"
```

## Format ID yang Dihasilkan

Format ID: `{KEY}-{YY}{MM}{NNNNN}`

Contoh:
- `SUP-241200001` (Supplier ID untuk Desember 2024, counter 1)
- `PAS-241200001` (Pasien ID untuk Desember 2024, counter 1)
- `BRG-241200001` (Barang ID untuk Desember 2024, counter 1)

## Catatan Penting

1. **Counter Reset**: Counter akan reset setiap bulan
2. **Thread Safe**: Menggunakan database transaction untuk memastikan ID unik
3. **Validation**: Key harus valid sesuai dengan daftar yang tersedia
4. **Error Handling**: Mengembalikan error yang informatif jika terjadi masalah

## Implementation Details

- **Controller**: `App\Http\Controllers\GeneratorController`
- **Utility**: `App\Utils\Generator`
- **Model**: `App\Models\MonthlySequence`
- **Routes**: Didefinisikan di `routes/api.php`

## Testing

Endpoint telah ditest dan berfungsi dengan baik. Route terdaftar dengan benar:
```
GET|HEAD  api/generate-number ........... GeneratorController@generateNumber
GET|HEAD  api/generate-number/keys .... GeneratorController@getAvailableKeys
```
