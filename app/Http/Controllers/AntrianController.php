<?php

namespace App\Http\Controllers;

use App\Http\Requests\AntrianRequest;
use App\Models\Antrian;
use App\Models\Pasien;
use App\Utils\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Antrian::with(['pasien', 'staff']);
        
        // Filter by status
        if (request()->has('status') && request('status')) {
            // Handle special case where frontend sends 'pasien' status
            // This might mean to filter by active antrians (menunggu/dipanggil)
            if (request('status') === 'pasien') {
                $query->whereIn('status', ['menunggu', 'dipanggil']);
            } else {
                $query->where('status', request('status'));
            }
        }
        
        // Filter by tanggal (date)
        if (request()->has('tanggal') && request('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        // Filter by is_active
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        // Search functionality
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhereHas('pasien', function($p) use ($search) {
                      $p->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('kode', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Pagination
        $perPage = request('per_page', request('page_size', 10));
        $page = request('page', 1);
        
        $antrians = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam', 'desc')
                         ->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $antrians->map(function($antrian) {
            $tanggalFormatted = $antrian->tanggal ? $antrian->tanggal->format('Y-m-d') : null;
            // jam is stored as TIME in database, Laravel returns it as string (H:i:s format)
            $jamFormatted = $antrian->jam ? substr($antrian->jam, 0, 5) : null; // Format H:i (tanpa detik)
            $tanggalFull = $tanggalFormatted && $jamFormatted ? $tanggalFormatted . ' ' . $jamFormatted : $tanggalFormatted;
            
            return [
                'id' => $antrian->id,
                'id_pasien' => $antrian->pasien_id,
                'pasien' => $antrian->pasien ? [
                    'id' => $antrian->pasien->id,
                    'kode' => $antrian->pasien->kode,
                    'nama' => $antrian->pasien->nama,
                    'alamat' => $antrian->pasien->alamat ?? null,
                    'no_telp' => $antrian->pasien->no_telp ?? null,
                    'email' => $antrian->pasien->email ?? null,
                    'tanggal_lahir' => $antrian->pasien->tanggal_lahir ? $antrian->pasien->tanggal_lahir->format('Y-m-d') : null,
                    'jenis_kelamin' => $antrian->pasien->jenis_kelamin ?? null,
                    'is_active' => $antrian->pasien->is_active ?? true,
                ] : null,
                'id_staff' => $antrian->staff_id,
                'staff' => $antrian->staff ? [
                    'id' => $antrian->staff->id,
                    'kode' => $antrian->staff->kode,
                    'nama' => $antrian->staff->nama,
                    'nip' => $antrian->staff->nip,
                    'jabatan' => $antrian->staff->jabatan,
                ] : null,
                'no_antrian' => $antrian->kode,
                'tanggal' => $tanggalFull,
                'status' => $antrian->status,
                'keterangan' => $antrian->keterangan,
                'barcode' => $antrian->kode, // Using kode as barcode
                'name' => $antrian->kode, // Using kode as name
                'is_active' => $antrian->is_active,
                'created_at' => $antrian->created_at,
                'updated_at' => $antrian->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $antrians->currentPage(),
            'page_size' => $antrians->perPage(),
            'total_pages' => $antrians->lastPage(),
            'total_rows' => $antrians->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AntrianRequest $request)
    {
        // Always generate unique kode on backend to avoid duplicates
        $kode = Generator::generateID('ANT');
        
        // Check if kode already exists (race condition protection)
        while (Antrian::where('kode', $kode)->exists()) {
            $kode = Generator::generateID('ANT');
        }
        
        $antrian = Antrian::create([
            'kode' => $kode,
            'pasien_id' => $request->pasien_id,
            'staff_id' => $request->staff_id ?? null,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam ?? now()->format('H:i:s'),
            'status' => $request->status ?? 'menunggu',
            'keterangan' => $request->keterangan ?? null,
            'is_active' => true,
        ]);

        return response()->json($antrian->load(['pasien', 'staff']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $antrian = Antrian::with(['pasien', 'staff'])->find($id);
        if (!$antrian) {
            return response()->json(['message' => 'Queue not found'], 404);
        }
        return response()->json($antrian);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AntrianRequest $request, string $id)
    {
        $antrian = Antrian::find($id);
        if (!$antrian) {
            return response()->json(['message' => 'Queue not found'], 404);
        }

        $antrian->update($request->validated());
        return response()->json($antrian->load(['pasien', 'staff']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $antrian = Antrian::find($id);
        if (!$antrian) {
            return response()->json(['message' => 'Queue not found'], 404);
        }

        $antrian->is_active = false;
        $antrian->save();
        return response()->json(['message' => 'Queue deactivated successfully']);
    }

    /**
     * Create new pasien and antrian together
     */
    public function createPasienAntrian(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate required fields
            $request->validate([
                'nama' => 'required|string|max:255',
                'no_telp' => 'required|string|max:20',
                'staff_id' => 'required|exists:staffs,id',
                'tanggal' => 'required|date',
                'alamat' => 'nullable|string',
                'email' => 'nullable|email|max:255',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'keterangan' => 'nullable|string',
            ]);

            // Create pasien
            $pasien = Pasien::create([
                'kode' => Generator::generateID('PAS'),
                'nama' => $request->nama,
                'alamat' => $request->alamat ?? null,
                'no_telp' => $request->no_telp,
                'email' => $request->email ?? null,
                'tanggal_lahir' => $request->tanggal_lahir ?? null,
                'jenis_kelamin' => $request->jenis_kelamin ?? null,
                'is_active' => true,
            ]);

            // Create antrian
            $kode = Generator::generateID('ANT');
            while (Antrian::where('kode', $kode)->exists()) {
                $kode = Generator::generateID('ANT');
            }

            $tanggalValue = $request->tanggal;
            $jamValue = $request->jam ?? now()->format('H:i:s');

            // If tanggal includes time, extract date and time separately
            if (strpos($tanggalValue, ' ') !== false) {
                $parts = explode(' ', $tanggalValue);
                $tanggalValue = $parts[0];
                $jamValue = $parts[1] ?? $jamValue;
            }

            $antrian = Antrian::create([
                'kode' => $kode,
                'pasien_id' => $pasien->id,
                'staff_id' => $request->staff_id,
                'tanggal' => $tanggalValue,
                'jam' => $jamValue,
                'status' => 'menunggu',
                'keterangan' => $request->keterangan ?? null,
                'is_active' => true,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pasien dan antrian berhasil dibuat',
                'pasien' => $pasien,
                'antrian' => $antrian->load(['pasien', 'staff']),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat pasien dan antrian',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
