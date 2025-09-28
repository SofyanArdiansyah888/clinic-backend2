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
        Perusahaan::create([
            'id' => 'PRS001',
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
        Staff::create([
            'id' => 'STAFF001',
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
            'id' => 'STAFF002',
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
            'id' => 'STAFF003',
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
        $banks = [
            [
                'id' => 'BNK001',
                'nama' => 'Bank Central Asia',
                'kode' => 'BCA',
                'alamat' => 'Menara BCA, Grand Indonesia',
                'telepon' => '021-500600',
                'email' => 'cs@bca.co.id',
                'is_active' => true,
            ],
            [
                'id' => 'BNK002',
                'nama' => 'Bank Mandiri',
                'kode' => 'MANDIRI',
                'alamat' => 'Plaza Mandiri, Jakarta',
                'telepon' => '021-52997777',
                'email' => 'callcenter@bankmandiri.co.id',
                'is_active' => true,
            ],
            [
                'id' => 'BNK003',
                'nama' => 'Bank Rakyat Indonesia',
                'kode' => 'BRI',
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
        $treatments = [
            [
                'id' => 'TRT001',
                'nama' => 'Konsultasi Umum',
                'deskripsi' => 'Konsultasi dengan dokter umum untuk pemeriksaan kesehatan rutin',
                'durasi' => 30,
                'harga' => 150000.00,
                'kategori' => 'Konsultasi',
                'is_active' => true,
            ],
            [
                'id' => 'TRT002',
                'nama' => 'Pemeriksaan Tekanan Darah',
                'deskripsi' => 'Pemeriksaan tekanan darah untuk monitoring kesehatan',
                'durasi' => 15,
                'harga' => 50000.00,
                'kategori' => 'Pemeriksaan',
                'is_active' => true,
            ],
            [
                'id' => 'TRT003',
                'nama' => 'Suntik Vitamin',
                'deskripsi' => 'Suntik vitamin untuk meningkatkan daya tahan tubuh',
                'durasi' => 20,
                'harga' => 200000.00,
                'kategori' => 'Terapi',
                'is_active' => true,
            ],
            [
                'id' => 'TRT004',
                'nama' => 'Pemeriksaan Lab Darah',
                'deskripsi' => 'Pemeriksaan laboratorium darah lengkap',
                'durasi' => 45,
                'harga' => 300000.00,
                'kategori' => 'Laboratorium',
                'is_active' => true,
            ],
            [
                'id' => 'TRT005',
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
        $suppliers = [
            [
                'id' => 'SUP001',
                'nama' => 'PT. Farmasi Sehat',
                'alamat' => 'Jl. Farmasi No. 100, Jakarta Barat',
                'no_telp' => '021-12345678',
                'email' => 'info@farmasisehat.com',
                'contact_person' => 'John Doe',
                'npwp' => '987654321098765',
                'is_active' => true,
            ],
            [
                'id' => 'SUP002',
                'nama' => 'CV. Alat Medis Jaya',
                'alamat' => 'Jl. Medis No. 200, Jakarta Timur',
                'no_telp' => '021-87654321',
                'email' => 'sales@alatmedis.com',
                'contact_person' => 'Jane Smith',
                'npwp' => '111222333444555',
                'is_active' => true,
            ],
            [
                'id' => 'SUP003',
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
        $barangs = [
            [
                'id' => 'BRG001',
                'nama' => 'Paracetamol 500mg',
                'kode' => 'PAR500',
                'kategori' => 'Obat',
                'satuan' => 'Tablet',
                'harga_beli' => 500.00,
                'harga_jual' => 1000.00,
                'stok_minimal' => 100,
                'stok_aktual' => 500,
                'is_active' => true,
            ],
            [
                'id' => 'BRG002',
                'nama' => 'Amoxicillin 500mg',
                'kode' => 'AMO500',
                'kategori' => 'Obat',
                'satuan' => 'Kapsul',
                'harga_beli' => 2000.00,
                'harga_jual' => 4000.00,
                'stok_minimal' => 50,
                'stok_aktual' => 200,
                'is_active' => true,
            ],
            [
                'id' => 'BRG003',
                'nama' => 'Tensimeter Digital',
                'kode' => 'TENS001',
                'kategori' => 'Alat Medis',
                'satuan' => 'Unit',
                'harga_beli' => 500000.00,
                'harga_jual' => 750000.00,
                'stok_minimal' => 2,
                'stok_aktual' => 5,
                'is_active' => true,
            ],
            [
                'id' => 'BRG004',
                'nama' => 'Stetoskop',
                'kode' => 'STET001',
                'kategori' => 'Alat Medis',
                'satuan' => 'Unit',
                'harga_beli' => 300000.00,
                'harga_jual' => 450000.00,
                'stok_minimal' => 3,
                'stok_aktual' => 8,
                'is_active' => true,
            ],
            [
                'id' => 'BRG005',
                'nama' => 'Syringe 5ml',
                'kode' => 'SYR5ML',
                'kategori' => 'Alat Medis',
                'satuan' => 'Pcs',
                'harga_beli' => 2500.00,
                'harga_jual' => 5000.00,
                'stok_minimal' => 100,
                'stok_aktual' => 500,
                'is_active' => true,
            ],
            [
                'id' => 'BRG006',
                'nama' => 'Alkohol 70%',
                'kode' => 'ALK70',
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
