<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Utils\Generator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Supplier::query();
        
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
        
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        // Pagination
        $perPage = request('per_page', 10);
        $page = request('page', 1);
        
        $suppliers = $query->orderBy('nama', 'asc')->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $suppliers->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'kode' => $supplier->kode,
                'nama' => $supplier->nama,
                'alamat' => $supplier->alamat,
                'no_telp' => $supplier->no_telp,
                'email' => $supplier->email,
                'contact_person' => $supplier->contact_person,
                'npwp' => $supplier->npwp,
                'is_active' => $supplier->is_active,
                'created_at' => $supplier->created_at,
                'updated_at' => $supplier->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $suppliers->currentPage(),
            'page_size' => $suppliers->perPage(),
            'total_pages' => $suppliers->total(),
            'total_rows' => $suppliers->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        $validated = $request->validated();
        
        // Generate kode if not provided
        if (!isset($validated['kode']) || empty($validated['kode'])) {
            $validated['kode'] = Generator::generateID('SUP');
        }
        
        $supplier = Supplier::create($validated);
        
        return response()->json([
            'id' => $supplier->id,
            'kode' => $supplier->kode,
            'nama' => $supplier->nama,
            'alamat' => $supplier->alamat,
            'no_telp' => $supplier->no_telp,
            'email' => $supplier->email,
            'contact_person' => $supplier->contact_person,
            'npwp' => $supplier->npwp,
            'is_active' => $supplier->is_active,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        return response()->json([
            'id' => $supplier->id,
            'kode' => $supplier->kode,
            'nama' => $supplier->nama,
            'alamat' => $supplier->alamat,
            'no_telp' => $supplier->no_telp,
            'email' => $supplier->email,
            'contact_person' => $supplier->contact_person,
            'npwp' => $supplier->npwp,
            'is_active' => $supplier->is_active,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->validated());
        
        return response()->json([
            'id' => $supplier->id,
            'kode' => $supplier->kode,
            'nama' => $supplier->nama,
            'alamat' => $supplier->alamat,
            'no_telp' => $supplier->no_telp,
            'email' => $supplier->email,
            'contact_person' => $supplier->contact_person,
            'npwp' => $supplier->npwp,
            'is_active' => $supplier->is_active,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response()->json(null, 204);
    }

    /**
     * Export supplier to CSV/Excel
     */
    public function export()
    {
        $query = Supplier::query();
        
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
        
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        $suppliers = $query->orderBy('nama', 'asc')->get();
        
        // Create CSV content
        $filename = 'supplier_export_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($suppliers) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['ID', 'Kode', 'Nama', 'Alamat', 'Telepon', 'Email', 'Contact Person', 'NPWP', 'Status', 'Tanggal Dibuat', 'Tanggal Diupdate']);
            
            // Add data
            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->id,
                    $supplier->kode,
                    $supplier->nama,
                    $supplier->alamat ?? '',
                    $supplier->no_telp ?? '',
                    $supplier->email ?? '',
                    $supplier->contact_person ?? '',
                    $supplier->npwp ?? '',
                    $supplier->is_active ? 'Aktif' : 'Tidak Aktif',
                    $supplier->created_at ? $supplier->created_at->format('Y-m-d H:i:s') : '',
                    $supplier->updated_at ? $supplier->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
