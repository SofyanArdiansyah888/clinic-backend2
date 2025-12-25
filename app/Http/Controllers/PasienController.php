<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasienRequest;
use App\Models\Pasien;
use App\Utils\Generator;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Pasien::query();
        
        if (request()->has('search') && request('search')) {
            $query->where(function($q) {
                $q->where('nama', 'like', '%' . request('search') . '%')
                  ->orWhere('kode', 'like', '%' . request('search') . '%')
                  ->orWhere('no_telp', 'like', '%' . request('search') . '%')
                  ->orWhere('email', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request()->has('nama') && request('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('kode') && request('kode')) {
            $query->where('kode', 'like', '%' . request('kode') . '%');
        }
        
        if (request()->has('jenis_kelamin') && request('jenis_kelamin')) {
            $query->where('jenis_kelamin', request('jenis_kelamin'));
        }
        
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        // Pagination
        $perPage = request('per_page', 10);
        $page = request('page', 1);
        
        $pasiens = $query->orderBy('nama', 'asc')->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $pasiens->map(function($pasien) {
            return [
                'id' => $pasien->id,
                'kode' => $pasien->kode,
                'nama' => $pasien->nama,
                'alamat' => $pasien->alamat,
                'no_telp' => $pasien->no_telp,
                'email' => $pasien->email,
                'tanggal_lahir' => $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('Y-m-d') : null,
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'is_active' => $pasien->is_active,
                'created_at' => $pasien->created_at,
                'updated_at' => $pasien->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $pasiens->currentPage(),
            'page_size' => $pasiens->perPage(),
            'total_pages' => $pasiens->total(),
            'total_rows' => $pasiens->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PasienRequest $request)
    {
        $validated = $request->validated();
        
        // Generate kode if not provided
        if (!isset($validated['kode']) || empty($validated['kode'])) {
            $validated['kode'] = Generator::generateID('PAS');
        }
        
        $pasien = Pasien::create($validated);
        
        return response()->json([
            'id' => $pasien->id,
            'kode' => $pasien->kode,
            'nama' => $pasien->nama,
            'alamat' => $pasien->alamat,
            'no_telp' => $pasien->no_telp,
            'email' => $pasien->email,
            'tanggal_lahir' => $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('Y-m-d') : null,
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'is_active' => $pasien->is_active,
            'created_at' => $pasien->created_at,
            'updated_at' => $pasien->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        
        return response()->json([
            'id' => $pasien->id,
            'kode' => $pasien->kode,
            'nama' => $pasien->nama,
            'alamat' => $pasien->alamat,
            'no_telp' => $pasien->no_telp,
            'email' => $pasien->email,
            'tanggal_lahir' => $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('Y-m-d') : null,
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'is_active' => $pasien->is_active,
            'created_at' => $pasien->created_at,
            'updated_at' => $pasien->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PasienRequest $request, string $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->update($request->validated());
        
        return response()->json([
            'id' => $pasien->id,
            'kode' => $pasien->kode,
            'nama' => $pasien->nama,
            'alamat' => $pasien->alamat,
            'no_telp' => $pasien->no_telp,
            'email' => $pasien->email,
            'tanggal_lahir' => $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('Y-m-d') : null,
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'is_active' => $pasien->is_active,
            'created_at' => $pasien->created_at,
            'updated_at' => $pasien->updated_at,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();
        return response()->json(null, 204);
    }

    /**
     * Export pasien to CSV/Excel
     */
    public function export()
    {
        $query = Pasien::query();
        
        // Get filter parameters from request
        $request = request();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('no_telp', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('nama') && $request->nama) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        
        if ($request->has('kode') && $request->kode) {
            $query->where('kode', 'like', '%' . $request->kode . '%');
        }
        
        if ($request->has('jenis_kelamin') && $request->jenis_kelamin) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        $pasiens = $query->orderBy('nama', 'asc')->get();
        
        // Create CSV content
        $filename = 'pasien_export_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($pasiens) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['ID', 'Kode', 'Nama', 'Alamat', 'Telepon', 'Email', 'Tanggal Lahir', 'Jenis Kelamin', 'Status', 'Tanggal Dibuat', 'Tanggal Diupdate']);
            
            // Add data
            foreach ($pasiens as $pasien) {
                fputcsv($file, [
                    $pasien->id,
                    $pasien->kode,
                    $pasien->nama,
                    $pasien->alamat ?? '',
                    $pasien->no_telp ?? '',
                    $pasien->email ?? '',
                    $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('Y-m-d') : '',
                    $pasien->jenis_kelamin ?? '',
                    $pasien->is_active ? 'Aktif' : 'Tidak Aktif',
                    $pasien->created_at ? $pasien->created_at->format('Y-m-d H:i:s') : '',
                    $pasien->updated_at ? $pasien->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Import pasien from CSV/Excel
     */
    public function import()
    {
        $request = request();
        
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);
        
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        try {
            if ($extension === 'csv') {
                $data = array_map('str_getcsv', file($file->getRealPath()));
                $header = array_shift($data);
            } else {
                // For Excel files, you might need to use a library like PhpSpreadsheet
                return response()->json(['message' => 'Excel import belum didukung, gunakan format CSV'], 400);
            }
            
            $imported = 0;
            $errors = [];
            
            foreach ($data as $index => $row) {
                try {
                    $rowData = array_combine($header, $row);
                    
                    // Map CSV columns to database fields
                    $pasienData = [
                        'kode' => $rowData['Kode'] ?? Generator::generateID('PAS'),
                        'nama' => $rowData['Nama'] ?? '',
                        'alamat' => $rowData['Alamat'] ?? null,
                        'no_telp' => $rowData['Telepon'] ?? null,
                        'email' => $rowData['Email'] ?? null,
                        'tanggal_lahir' => !empty($rowData['Tanggal Lahir']) ? $rowData['Tanggal Lahir'] : null,
                        'jenis_kelamin' => $rowData['Jenis Kelamin'] ?? null,
                        'is_active' => isset($rowData['Status']) && strtolower($rowData['Status']) === 'aktif' ? true : false,
                    ];
                    
                    if (empty($pasienData['nama'])) {
                        $errors[] = "Baris " . ($index + 2) . ": Nama wajib diisi";
                        continue;
                    }
                    
                    Pasien::create($pasienData);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            return response()->json([
                'message' => "Berhasil mengimport {$imported} data pasien",
                'imported' => $imported,
                'errors' => $errors,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengimport data: ' . $e->getMessage()], 400);
        }
    }
}
