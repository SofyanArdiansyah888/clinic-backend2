<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Staff;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Staff::query();
        
        if (request()->has('search') && request('search')) {
            $query->where(function($q) {
                $q->where('nama', 'like', '%' . request('search') . '%')
                  ->orWhere('kode', 'like', '%' . request('search') . '%')
                  ->orWhere('nip', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request()->has('nama') && request('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('jabatan') && request('jabatan')) {
            $query->where('jabatan', request('jabatan'));
        }
        
        if (request()->has('departemen') && request('departemen')) {
            $query->where('departemen', request('departemen'));
        }
        
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        // Pagination
        $perPage = request('per_page', 10);
        $page = request('page', 1);
        
        $staffs = $query->orderBy('nama', 'asc')->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $staffs->map(function($staff) {
            return [
                'id' => $staff->id,
                'kode' => $staff->kode,
                'nama' => $staff->nama,
                'nip' => $staff->nip,
                'jabatan' => $staff->jabatan,
                'departemen' => $staff->departemen,
                'no_telp' => $staff->no_telp,
                'email' => $staff->email,
                'alamat' => $staff->alamat,
                'tanggal_bergabung' => $staff->tanggal_bergabung,
                'is_active' => $staff->is_active,
                'created_at' => $staff->created_at,
                'updated_at' => $staff->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $staffs->currentPage(),
            'page_size' => $staffs->perPage(),
            'total_pages' => $staffs->total(),
            'total_rows' => $staffs->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffRequest $request)
    {
        $staff = Staff::create($request->validated());
        
        return response()->json([
            'id' => $staff->id,
            'kode' => $staff->kode,
            'nama' => $staff->nama,
            'nip' => $staff->nip,
            'jabatan' => $staff->jabatan,
            'departemen' => $staff->departemen,
            'no_telp' => $staff->no_telp,
            'email' => $staff->email,
            'alamat' => $staff->alamat,
            'tanggal_bergabung' => $staff->tanggal_bergabung,
            'is_active' => $staff->is_active,
            'created_at' => $staff->created_at,
            'updated_at' => $staff->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $staff = Staff::findOrFail($id);
        return response()->json([
            'id' => $staff->id,
            'kode' => $staff->kode,
            'nama' => $staff->nama,
            'nip' => $staff->nip,
            'jabatan' => $staff->jabatan,
            'departemen' => $staff->departemen,
            'no_telp' => $staff->no_telp,
            'email' => $staff->email,
            'alamat' => $staff->alamat,
            'tanggal_bergabung' => $staff->tanggal_bergabung,
            'is_active' => $staff->is_active,
            'created_at' => $staff->created_at,
            'updated_at' => $staff->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffRequest $request, string $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->update($request->validated());
        $staff->refresh();
        
        return response()->json([
            'id' => $staff->id,
            'kode' => $staff->kode,
            'nama' => $staff->nama,
            'nip' => $staff->nip,
            'jabatan' => $staff->jabatan,
            'departemen' => $staff->departemen,
            'no_telp' => $staff->no_telp,
            'email' => $staff->email,
            'alamat' => $staff->alamat,
            'tanggal_bergabung' => $staff->tanggal_bergabung,
            'is_active' => $staff->is_active,
            'created_at' => $staff->created_at,
            'updated_at' => $staff->updated_at,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return response()->json(null, 204);
    }

    /**
     * Export staff to CSV/Excel
     */
    public function export()
    {
        $query = Staff::query();
        
        // Get filter parameters from request
        $request = request();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('nama') && $request->nama) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        
        if ($request->has('jabatan') && $request->jabatan) {
            $query->where('jabatan', $request->jabatan);
        }
        
        if ($request->has('departemen') && $request->departemen) {
            $query->where('departemen', $request->departemen);
        }
        
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        $staffs = $query->orderBy('nama', 'asc')->get();
        
        // Create CSV content
        $filename = 'staff_export_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($staffs) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['ID', 'Kode', 'Nama', 'NIP', 'Jabatan', 'Departemen', 'Telepon', 'Email', 'Alamat', 'Tanggal Bergabung', 'Status', 'Tanggal Dibuat', 'Tanggal Diupdate']);
            
            // Add data
            foreach ($staffs as $staff) {
                fputcsv($file, [
                    $staff->id,
                    $staff->kode,
                    $staff->nama,
                    $staff->nip,
                    $staff->jabatan,
                    $staff->departemen,
                    $staff->no_telp ?? '',
                    $staff->email ?? '',
                    $staff->alamat ?? '',
                    $staff->tanggal_bergabung ? $staff->tanggal_bergabung->format('Y-m-d') : '',
                    $staff->is_active ? 'Aktif' : 'Tidak Aktif',
                    $staff->created_at ? $staff->created_at->format('Y-m-d H:i:s') : '',
                    $staff->updated_at ? $staff->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
