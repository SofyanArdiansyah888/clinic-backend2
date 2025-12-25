<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Utils\Generator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Barang::query();
        
        if (request()->has('search') && request('search')) {
            $query->where(function($q) {
                $q->where('nama', 'like', '%' . request('search') . '%')
                  ->orWhere('kode', 'like', '%' . request('search') . '%')
                  ->orWhere('kategori', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request()->has('nama') && request('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('kode') && request('kode')) {
            $query->where('kode', 'like', '%' . request('kode') . '%');
        }
        
        if (request()->has('kategori') && request('kategori')) {
            $query->where('kategori', request('kategori'));
        }
        
        if (request()->has('satuan') && request('satuan')) {
            $query->where('satuan', request('satuan'));
        }
        
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        // Filter berdasarkan lokasi barang (apotek/gudang)
        if (request()->has('lokasi_barang')) {
            $lokasi = request('lokasi_barang');
            $query->where('lokasi_barang', $lokasi);
        }
        
        // Pagination
        $perPage = request('per_page', request('page_size', 10));
        $page = request('page', 1);
        
        $barangs = $query->orderBy('nama', 'asc')->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $barangs->map(function($barang) {
            return [
                'id' => $barang->id,
                'kode' => $barang->kode,
                'nama' => $barang->nama,
                'kategori' => $barang->kategori,
                'lokasi_barang' => $barang->lokasi_barang,
                'satuan' => $barang->satuan,
                'harga_beli' => $barang->harga_beli,
                'harga_jual' => $barang->harga_jual,
                'stok_minimal' => $barang->stok_minimal,
                'stok_aktual' => $barang->stok_aktual,
                'is_active' => $barang->is_active,
                'created_at' => $barang->created_at,
                'updated_at' => $barang->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $barangs->currentPage(),
            'page_size' => $barangs->perPage(),
            'total_pages' => $barangs->total(),
            'total_rows' => $barangs->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BarangRequest $request)
    {
        $validated = $request->validated();
        
        // Generate kode if not provided
        if (!isset($validated['kode']) || empty($validated['kode'])) {
            $validated['kode'] = Generator::generateID('BRG');
        }
        
        $barang = Barang::create($validated);
        
        return response()->json([
            'id' => $barang->id,
            'kode' => $barang->kode,
            'nama' => $barang->nama,
            'kategori' => $barang->kategori,
            'lokasi_barang' => $barang->lokasi_barang,
            'satuan' => $barang->satuan,
            'harga_beli' => $barang->harga_beli,
            'harga_jual' => $barang->harga_jual,
            'stok_minimal' => $barang->stok_minimal,
            'stok_aktual' => $barang->stok_aktual,
            'is_active' => $barang->is_active,
            'created_at' => $barang->created_at,
            'updated_at' => $barang->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barang = Barang::findOrFail($id);
        
        return response()->json([
            'id' => $barang->id,
            'kode' => $barang->kode,
            'nama' => $barang->nama,
            'kategori' => $barang->kategori,
            'lokasi_barang' => $barang->lokasi_barang,
            'satuan' => $barang->satuan,
            'harga_beli' => $barang->harga_beli,
            'harga_jual' => $barang->harga_jual,
            'stok_minimal' => $barang->stok_minimal,
            'stok_aktual' => $barang->stok_aktual,
            'is_active' => $barang->is_active,
            'created_at' => $barang->created_at,
            'updated_at' => $barang->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BarangRequest $request, string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->update($request->validated());
        
        return response()->json([
            'id' => $barang->id,
            'kode' => $barang->kode,
            'nama' => $barang->nama,
            'kategori' => $barang->kategori,
            'lokasi_barang' => $barang->lokasi_barang,
            'satuan' => $barang->satuan,
            'harga_beli' => $barang->harga_beli,
            'harga_jual' => $barang->harga_jual,
            'stok_minimal' => $barang->stok_minimal,
            'stok_aktual' => $barang->stok_aktual,
            'is_active' => $barang->is_active,
            'created_at' => $barang->created_at,
            'updated_at' => $barang->updated_at,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return response()->json(null, 204);
    }

    /**
     * Update stock quantity
     */
    public function updateStock()
    {
        $request = request();
        $request->validate([
            'stok_aktual' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($request->route('barang'));
        $barang->update(['stok_aktual' => $request->stok_aktual]);
        
        return response()->json([
            'message' => 'Stock updated successfully',
            'barang' => $barang
        ]);
    }

    /**
     * Export barang to CSV/Excel
     */
    public function export()
    {
        $query = Barang::query();
        
        // Get filter parameters from request
        $request = request();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('nama') && $request->nama) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        
        if ($request->has('kode') && $request->kode) {
            $query->where('kode', 'like', '%' . $request->kode . '%');
        }
        
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->has('satuan') && $request->satuan) {
            $query->where('satuan', $request->satuan);
        }
        
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        // Filter berdasarkan lokasi barang (apotek/gudang)
        if ($request->has('lokasi_barang')) {
            $lokasi = $request->lokasi_barang;
            $query->where('lokasi_barang', $lokasi);
        }
        
        $barangs = $query->orderBy('nama', 'asc')->get();
        
        // Create CSV content
        $filename = 'barang_export_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($barangs) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['ID', 'Kode', 'Nama', 'Kategori', 'Satuan', 'Harga Beli', 'Harga Jual', 'Stok Minimal', 'Stok Aktual', 'Status', 'Tanggal Dibuat', 'Tanggal Diupdate']);
            
            // Add data
            foreach ($barangs as $barang) {
                fputcsv($file, [
                    $barang->id,
                    $barang->kode,
                    $barang->nama,
                    $barang->kategori,
                    $barang->satuan,
                    number_format($barang->harga_beli, 2, ',', '.'),
                    number_format($barang->harga_jual, 2, ',', '.'),
                    $barang->stok_minimal,
                    $barang->stok_aktual,
                    $barang->is_active ? 'Aktif' : 'Tidak Aktif',
                    $barang->created_at ? $barang->created_at->format('Y-m-d H:i:s') : '',
                    $barang->updated_at ? $barang->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
