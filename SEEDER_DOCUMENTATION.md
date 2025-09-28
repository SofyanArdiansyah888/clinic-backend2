# Database Seeder Documentation

## Overview
File `DatabaseSeeder.php` telah dikonfigurasi untuk menyediakan data startup lengkap untuk aplikasi klinik. Seeder ini akan membuat data awal yang diperlukan untuk menjalankan aplikasi klinik.

## Data yang akan di-seed:

### 1. Perusahaan/Klinik
- Data klinik default: "Klinik Sehat Mandiri"
- ID: PRS001

### 2. Users (Akun Login)
- **Admin**: 
  - Username: `admin`
  - Password: `admin123`
  - Email: admin@kliniksehat.com
- **Doctor**: 
  - Username: `doctor`
  - Password: `doctor123`
  - Email: doctor@kliniksehat.com
- **Staff**: 
  - Username: `staff`
  - Password: `staff123`
  - Email: staff@kliniksehat.com

### 3. Staff (Data Karyawan)
- 3 staff default (2 dokter, 1 perawat)
- Dengan data lengkap (NIP, jabatan, alamat, dll)

### 4. Banks
- 3 bank default: BCA, Mandiri, BRI
- Untuk transaksi pembayaran

### 5. Treatments (Perawatan)
- 5 jenis perawatan default:
  - Konsultasi Umum (Rp 150.000)
  - Pemeriksaan Tekanan Darah (Rp 50.000)
  - Suntik Vitamin (Rp 200.000)
  - Pemeriksaan Lab Darah (Rp 300.000)
  - Konsultasi Spesialis (Rp 500.000)

### 6. Suppliers
- 3 supplier default untuk obat dan alat medis

### 7. Barang/Obat
- 6 item default:
  - Paracetamol 500mg
  - Amoxicillin 500mg
  - Tensimeter Digital
  - Stetoskop
  - Syringe 5ml
  - Alkohol 70%

## Cara Menjalankan Seeder

### 1. Fresh Migration + Seeder (Recommended untuk pertama kali)
```bash
php artisan migrate:fresh --seed
```

### 2. Hanya menjalankan seeder (jika tabel sudah ada)
```bash
php artisan db:seed
```

### 3. Menjalankan seeder spesifik (jika ada seeder lain)
```bash
php artisan db:seed --class=DatabaseSeeder
```

## Catatan Penting

1. **Password Default**: Pastikan untuk mengganti password default setelah login pertama
2. **Data Demo**: Data yang di-seed adalah data demo, sesuaikan dengan kebutuhan klinik yang sebenarnya
3. **ID Hardcoded**: ID menggunakan format hardcoded (PRS001, STAFF001, dll) untuk konsistensi
4. **Email**: Pastikan email yang digunakan tidak bentrok dengan data existing

## Troubleshooting

### Error: "Table doesn't exist"
- Pastikan migration sudah dijalankan terlebih dahulu
- Gunakan `php artisan migrate:fresh --seed`

### Error: "Duplicate entry"
- Pastikan database kosong atau gunakan `migrate:fresh`
- Atau hapus data existing sebelum menjalankan seeder

### Error: "Model not found"
- Pastikan semua model sudah di-import di DatabaseSeeder.php
- Pastikan namespace model sudah benar

## Setelah Seeder Selesai

1. Login dengan akun admin (admin/admin123)
2. Ganti password default
3. Sesuaikan data perusahaan dengan data klinik yang sebenarnya
4. Tambah/edit staff sesuai kebutuhan
5. Sesuaikan treatments dan harga sesuai layanan klinik
6. Import data obat dan alat medis yang sebenarnya

## Catatan Penting

**Sistem Penomoran ID:** Sistem penomoran ID otomatis sekarang menggunakan counter dari tabel masing-masing, tidak lagi memerlukan tabel `MonthlySequence` terpisah. Endpoint `/api/generate-number?key=SUP` akan menghasilkan ID seperti `SUP-241200001` berdasarkan jumlah record di tabel `suppliers`.
