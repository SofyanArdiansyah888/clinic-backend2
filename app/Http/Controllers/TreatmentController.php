<?php

namespace App\Http\Controllers;

use App\Http\Requests\TreatmentRequest;
use App\Models\Treatment;
use App\Utils\Generator;

class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Treatment::query();
        
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
        
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        // Pagination
        $perPage = request('per_page', 10);
        $page = request('page', 1);
        
        $treatments = $query->orderBy('nama', 'asc')->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $treatments->map(function($treatment) {
            return [
                'id' => $treatment->id,
                'kode' => $treatment->kode,
                'nama' => $treatment->nama,
                'deskripsi' => $treatment->deskripsi,
                'durasi' => $treatment->durasi,
                'harga' => $treatment->harga,
                'kategori' => $treatment->kategori,
                'is_active' => $treatment->is_active,
                'created_at' => $treatment->created_at,
                'updated_at' => $treatment->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $treatments->currentPage(),
            'page_size' => $treatments->perPage(),
            'total_pages' => $treatments->total(),
            'total_rows' => $treatments->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TreatmentRequest $request)
    {
        $validated = $request->validated();
        
        // Generate kode if not provided
        if (!isset($validated['kode']) || empty($validated['kode'])) {
            $validated['kode'] = Generator::generateID('TRT');
        }
        
        $treatment = Treatment::create($validated);
        
        return response()->json([
            'id' => $treatment->id,
            'kode' => $treatment->kode,
            'nama' => $treatment->nama,
            'deskripsi' => $treatment->deskripsi,
            'durasi' => $treatment->durasi,
            'harga' => $treatment->harga,
            'kategori' => $treatment->kategori,
            'is_active' => $treatment->is_active,
            'created_at' => $treatment->created_at,
            'updated_at' => $treatment->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $treatment = Treatment::findOrFail($id);
        
        return response()->json([
            'id' => $treatment->id,
            'kode' => $treatment->kode,
            'nama' => $treatment->nama,
            'deskripsi' => $treatment->deskripsi,
            'durasi' => $treatment->durasi,
            'harga' => $treatment->harga,
            'kategori' => $treatment->kategori,
            'is_active' => $treatment->is_active,
            'created_at' => $treatment->created_at,
            'updated_at' => $treatment->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TreatmentRequest $request, string $id)
    {
        $treatment = Treatment::findOrFail($id);
        $treatment->update($request->validated());
        
        return response()->json([
            'id' => $treatment->id,
            'kode' => $treatment->kode,
            'nama' => $treatment->nama,
            'deskripsi' => $treatment->deskripsi,
            'durasi' => $treatment->durasi,
            'harga' => $treatment->harga,
            'kategori' => $treatment->kategori,
            'is_active' => $treatment->is_active,
            'created_at' => $treatment->created_at,
            'updated_at' => $treatment->updated_at,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $treatment = Treatment::findOrFail($id);
        $treatment->delete();
        return response()->json(null, 204);
    }

    /**
     * Export treatment to CSV/Excel
     */
    public function export()
    {
        $query = Treatment::query();
        
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
        
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        $treatments = $query->orderBy('nama', 'asc')->get();
        
        // Create CSV content
        $filename = 'treatment_export_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($treatments) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['ID', 'Kode', 'Nama', 'Deskripsi', 'Durasi (menit)', 'Harga', 'Kategori', 'Status', 'Tanggal Dibuat', 'Tanggal Diupdate']);
            
            // Add data
            foreach ($treatments as $treatment) {
                fputcsv($file, [
                    $treatment->id,
                    $treatment->kode,
                    $treatment->nama,
                    $treatment->deskripsi ?? '',
                    $treatment->durasi,
                    number_format($treatment->harga, 2, ',', '.'),
                    $treatment->kategori,
                    $treatment->is_active ? 'Aktif' : 'Tidak Aktif',
                    $treatment->created_at ? $treatment->created_at->format('Y-m-d H:i:s') : '',
                    $treatment->updated_at ? $treatment->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
