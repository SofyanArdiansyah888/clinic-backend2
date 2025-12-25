<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Staff;
use App\Models\Bank;
use App\Models\Treatment;
use App\Models\Supplier;
use App\Models\Barang;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Data Perusahaan/Klinik
        $this->seedPerusahaan();

        // 2. Seed User Admin
        $this->seedUsers();

        // 3. Seed Staff Default
        $this->seedStaff();

        // 4. Seed Bank
        $this->seedBanks();

        // 5. Seed Treatments/Perawatan
        $this->seedTreatments();

        // 6. Seed Suppliers
        $this->seedSuppliers();

        // 7. Seed Barang/Obat
        $this->seedBarangs();
    }


    private function seedPerusahaan()
    {
        // Sesuai dengan struktur tabel perusahaans (migration: create_perusahaans_table)
        // id: auto increment (tidak perlu di-set)
        // kode: string, unique
        // nama: string
        // alamat: text
        // telepon: string
        // email: string
        // website: string, nullable
        // npwp: string, nullable
        // is_active: boolean, default true
        // timestamps: otomatis
        Perusahaan::create([
            'kode' => 'PRS001',
            'nama' => 'Klinik Sehat Mandiri',
            'alamat' => 'Jl. Kesehatan No. 123, Jakarta Pusat 10110',
            'telepon' => '021-12345678',
            'email' => 'info@kliniksehat.com',
            'website' => 'www.kliniksehat.com',
            'npwp' => '123456789012345', 
            'is_active' => true,
        ]);

        $this->command->info('Perusahaan data seeded successfully.');
    }

    private function seedUsers()
    {
        // Sesuai dengan struktur tabel users (migration: create_users_table)
        // id: auto increment (tidak perlu di-set)
        // name: string
        // email: string, unique
        // username: string, unique
        // email_verified_at: timestamp, nullable
        // password: string
        // role: enum('admin', 'staff', 'doctor'), default 'staff'
        // is_active: boolean, default true
        // remember_token: string, nullable
        // timestamps: otomatis

        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kliniksehat.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Doctor User
        User::create([
            'name' => 'Dr. Sarah Wilson',
            'email' => 'doctor@kliniksehat.com',
            'username' => 'doctor',
            'password' => Hash::make('doctor123'),
            'role' => 'doctor',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Staff User
        User::create([
            'name' => 'Maria Garcia',
            'email' => 'staff@kliniksehat.com',
            'username' => 'staff',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Users seeded successfully.');
    }

    private function seedStaff()
    { 
        // Sesuai dengan struktur tabel staffs (migration: create_staffs_table)
        // id: auto increment (tidak perlu di-set)
        // kode: string, unique
        // nama: string
        // nip: string, unique
        // jabatan: string
        // departemen: string
        // no_telp: string, nullable
        // email: string, nullable
        // alamat: text, nullable
        // tanggal_bergabung: date
        // is_active: boolean, default true
        // timestamps: otomatis

        Staff::create([
            'kode' => 'STAFF001',
            'nama' => 'Dr. Sarah Wilson',
            'nip' => 'DOC001',
            'jabatan' => 'Dokter Utama',
            'departemen' => 'Medis',
            'no_telp' => '081234567890',
            'email' => 'doctor@kliniksehat.com',
            'alamat' => 'Jl. Dokter No. 456, Jakarta Selatan',
            'tanggal_bergabung' => now()->subMonths(12),
            'is_active' => true,
        ]);

        Staff::create([
            'kode' => 'STAFF002',
            'nama' => 'Maria Garcia',
            'nip' => 'STF001',
            'jabatan' => 'Perawat',
            'departemen' => 'Keperawatan',
            'no_telp' => '081234567891',
            'email' => 'staff@kliniksehat.com',
            'alamat' => 'Jl. Perawat No. 789, Jakarta Utara',
            'tanggal_bergabung' => now()->subMonths(6),
            'is_active' => true,
        ]);

        Staff::create([
            'kode' => 'STAFF003',
            'nama' => 'Dr. Ahmad Rahman',
            'nip' => 'DOC002',
            'jabatan' => 'Dokter Spesialis',
            'departemen' => 'Spesialis',
            'no_telp' => '081234567892',
            'email' => 'ahmad@kliniksehat.com',
            'alamat' => 'Jl. Spesialis No. 321, Jakarta Timur',
            'tanggal_bergabung' => now()->subMonths(3),
            'is_active' => true,
        ]);

        $this->command->info('Staff data seeded successfully.');
    }

    private function seedBanks()
    {
        // Sesuai dengan struktur tabel banks (migration: create_banks_table)
        // id: auto increment (tidak perlu di-set)
        // kode: string, unique
        // nama: string
        // alamat: text
        // telepon: string
        // email: string
        // is_active: boolean, default true
        // timestamps: otomatis

        $banks = [
            [
                'kode' => 'BCA',
                'nama' => 'Bank Central Asia',
                'alamat' => 'Menara BCA, Grand Indonesia',
                'telepon' => '021-500600',
                'email' => 'cs@bca.co.id',
                'is_active' => true,
            ],
            [
                'kode' => 'MANDIRI',
                'nama' => 'Bank Mandiri',
                'alamat' => 'Plaza Mandiri, Jakarta',
                'telepon' => '021-52997777',
                'email' => 'callcenter@bankmandiri.co.id',
                'is_active' => true,
            ],
            [
                'kode' => 'BRI',
                'nama' => 'Bank Rakyat Indonesia',
                'alamat' => 'Kantor Pusat BRI, Jakarta',
                'telepon' => '021-5155666',
                'email' => 'info@bri.co.id',
                'is_active' => true,
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }

        $this->command->info('Banks seeded successfully.');
    }

    private function seedTreatments()
    {
        // Sesuai dengan struktur tabel treatments (migration: create_treatments_table)
        // id: auto increment (tidak perlu di-set)
        // kode: string, unique
        // nama: string
        // deskripsi: text, nullable
        // durasi: integer (dalam menit)
        // harga: decimal(10,2), default 0
        // kategori: string
        // is_active: boolean, default true
        // timestamps: otomatis

        $treatments = [
            [
                'kode' => 'TRT001',
                'nama' => 'Konsultasi Umum',
                'deskripsi' => 'Konsultasi dengan dokter umum untuk pemeriksaan kesehatan rutin',
                'durasi' => 30,
                'harga' => 150000.00,
                'kategori' => 'Konsultasi',
                'is_active' => true,
            ],
            [
                'kode' => 'TRT002',
                'nama' => 'Pemeriksaan Tekanan Darah',
                'deskripsi' => 'Pemeriksaan tekanan darah untuk monitoring kesehatan',
                'durasi' => 15,
                'harga' => 50000.00,
                'kategori' => 'Pemeriksaan',
                'is_active' => true,
            ],
            [
                'kode' => 'TRT003',
                'nama' => 'Suntik Vitamin',
                'deskripsi' => 'Suntik vitamin untuk meningkatkan daya tahan tubuh',
                'durasi' => 20,
                'harga' => 200000.00,
                'kategori' => 'Terapi',
                'is_active' => true,
            ],
            [
                'kode' => 'TRT004',
                'nama' => 'Pemeriksaan Lab Darah',
                'deskripsi' => 'Pemeriksaan laboratorium darah lengkap',
                'durasi' => 45,
                'harga' => 300000.00,
                'kategori' => 'Laboratorium',
                'is_active' => true,
            ],
            [
                'kode' => 'TRT005',
                'nama' => 'Konsultasi Spesialis',
                'deskripsi' => 'Konsultasi dengan dokter spesialis',
                'durasi' => 60,
                'harga' => 500000.00,
                'kategori' => 'Konsultasi',
                'is_active' => true,
            ],
        ];

        foreach ($treatments as $treatment) {
            Treatment::create($treatment);
        }

        $this->command->info('Treatments seeded successfully.');
    }

    private function seedSuppliers()
    {
        // Sesuai dengan struktur tabel suppliers (migration: create_suppliers_table)
        // id: auto increment (tidak perlu di-set)
        // kode: string, unique
        // nama: string
        // alamat: text, nullable
        // no_telp: string, nullable
        // email: string, nullable
        // contact_person: string, nullable
        // npwp: string, nullable
        // is_active: boolean, default true
        // timestamps: otomatis

        $suppliers = [
            [
                'kode' => 'SUP001',
                'nama' => 'PT. Farmasi Sehat',
                'alamat' => 'Jl. Farmasi No. 100, Jakarta Barat',
                'no_telp' => '021-12345678',
                'email' => 'info@farmasisehat.com',
                'contact_person' => 'John Doe',
                'npwp' => '987654321098765',
                'is_active' => true,
            ],
            [
                'kode' => 'SUP002',
                'nama' => 'CV. Alat Medis Jaya',
                'alamat' => 'Jl. Medis No. 200, Jakarta Timur',
                'no_telp' => '021-87654321',
                'email' => 'sales@alatmedis.com',
                'contact_person' => 'Jane Smith',
                'npwp' => '111222333444555',
                'is_active' => true,
            ],
            [
                'kode' => 'SUP003',
                'nama' => 'PT. Suplai Klinik',
                'alamat' => 'Jl. Klinik No. 300, Jakarta Selatan',
                'no_telp' => '021-555666777',
                'email' => 'order@suplaiklinik.com',
                'contact_person' => 'Bob Johnson',
                'npwp' => '666777888999000',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('Suppliers seeded successfully.');
    }

    private function seedBarangs()
    {
        // Sesuai dengan struktur tabel barangs (migration: create_barangs_table)
        // id: auto increment (tidak perlu di-set)
        // kode: string, unique
        // nama: string
        // kategori: string
        // satuan: string
        // harga_beli: decimal(10,2), default 0
        // harga_jual: decimal(10,2), default 0
        // stok_minimal: integer, default 0
        // stok_aktual: integer, default 0
        // is_active: boolean, default true
        // timestamps: otomatis

        $barangs = [
            [
                'kode' => 'PAR500',
                'nama' => 'Paracetamol 500mg',
                'kategori' => 'Obat',
                'satuan' => 'Tablet',
                'harga_beli' => 500.00,
                'harga_jual' => 1000.00,
                'stok_minimal' => 100,
                'stok_aktual' => 500,
                'is_active' => true,
            ],
            [
                'kode' => 'AMO500',
                'nama' => 'Amoxicillin 500mg',
                'kategori' => 'Obat',
                'satuan' => 'Kapsul',
                'harga_beli' => 2000.00,
                'harga_jual' => 4000.00,
                'stok_minimal' => 50,
                'stok_aktual' => 200,
                'is_active' => true,
            ],
            [
                'kode' => 'TENS001',
                'nama' => 'Tensimeter Digital',
                'kategori' => 'Alat Medis',
                'satuan' => 'Unit',
                'harga_beli' => 500000.00,
                'harga_jual' => 750000.00,
                'stok_minimal' => 2,
                'stok_aktual' => 5,
                'is_active' => true,
            ],
            [
                'kode' => 'STET001',
                'nama' => 'Stetoskop',
                'kategori' => 'Alat Medis',
                'satuan' => 'Unit',
                'harga_beli' => 300000.00,
                'harga_jual' => 450000.00,
                'stok_minimal' => 3,
                'stok_aktual' => 8,
                'is_active' => true,
            ],
            [
                'kode' => 'SYR5ML',
                'nama' => 'Syringe 5ml',
                'kategori' => 'Alat Medis',
                'satuan' => 'Pcs',
                'harga_beli' => 2500.00,
                'harga_jual' => 5000.00,
                'stok_minimal' => 100,
                'stok_aktual' => 500,
                'is_active' => true,
            ],
            [
                'kode' => 'ALK70',
                'nama' => 'Alkohol 70%',
                'kategori' => 'Disinfektan',
                'satuan' => 'Botol',
                'harga_beli' => 15000.00,
                'harga_jual' => 25000.00,
                'stok_minimal' => 10,
                'stok_aktual' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }

        $this->command->info('Barangs seeded successfully.');
    }
}
