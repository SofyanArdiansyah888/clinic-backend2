<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerawatanRequest;
use App\Models\Perawatan;
use App\Models\PerawatanResep;
use App\Models\PerawatanResepBarang;
use App\Models\PerawatanTindakan;
use App\Utils\Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PerawatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Perawatan::with(['pasien', 'treatment', 'staff', 'antrian', 'reseps.barangs', 'tindakans.treatment', 'tindakans.beautician']);
        
        if (request()->has('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        if (request()->has('pasien_id')) {
            $query->where('pasien_id', request('pasien_id'));
        }
        
        if (request()->has('staff_id')) {
            $query->where('staff_id', request('staff_id'));
        }
        
        $perawatans = $query->orderBy('tanggal', 'desc')
                            ->orderBy('jam_mulai', 'asc')
                            ->get();
        
        return response()->json($perawatans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PerawatanRequest $request): JsonResponse
    {
        // Check if request has details array (new format for treatment reguler)
        if ($request->has('details') && is_array($request->details)) {
            DB::beginTransaction();
            try {
                $perawatans = [];
                
                foreach ($request->details as $detail) {
                    $perawatan = Perawatan::create([
                        'kode' => Generator::generateID('PRW'),
                        'pasien_id' => $request->pasien_id,
                        'staff_id' => $request->staff_id,
                        'treatment_id' => $detail['treatment_id'],
                        'tanggal' => $request->tanggal,
                        'jam_mulai' => '00:00:00',
                        'jam_selesai' => '00:00:00',
                        'diagnosa' => $request->diagnosa,
                        'tindakan' => $detail['tindakan'],
                        'biaya' => $detail['total'] ?? $detail['harga'] ?? 0,
                        'status' => 'planned',
                        'catatan' => $detail['catatan'] ?? null,
                        'is_active' => true,
                    ]);
                    
                    $perawatans[] = $perawatan->load(['pasien', 'treatment', 'staff', 'antrian']);
                }
                
                DB::commit();
                
                return response()->json([
                    'message' => 'Treatment berhasil diproses ke kasir pembayaran',
                    'data' => $perawatans
                ], 201);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message' => 'Gagal memproses treatment: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // New detail perawatan format or old single perawatan format
            DB::beginTransaction();
            try {
                $validated = $request->validated();
                $validated['kode'] = Generator::generateID('PRW');
                $validated['is_active'] = $validated['is_active'] ?? true;
                
                $perawatan = Perawatan::create($validated);
                
                // Handle reseps if provided
                if ($request->has('reseps') && is_array($request->reseps)) {
                    foreach ($request->reseps as $resepData) {
                        $resep = PerawatanResep::create([
                            'perawatan_id' => $perawatan->id,
                            'antrian_id' => $resepData['antrian_id'],
                            'pasien_id' => $perawatan->pasien_id,
                            'staff_id' => $perawatan->staff_id,
                            'kode' => Generator::generateID('RES'),
                            'tanggal' => $resepData['tanggal'] ?? now(),
                            'status' => $resepData['status'] ?? 'draft',
                            'is_active' => true,
                        ]);
                        
                        // Handle barangs
                        if (isset($resepData['barangs']) && is_array($resepData['barangs'])) {
                            foreach ($resepData['barangs'] as $barangData) {
                                PerawatanResepBarang::create([
                                    'perawatan_resep_id' => $resep->id,
                                    'barang_id' => $barangData['barang_id'],
                                    'kode_barang' => $barangData['kode_barang'],
                                    'nama_barang' => $barangData['nama_barang'],
                                    'jumlah' => $barangData['jumlah'],
                                    'unit' => $barangData['unit'],
                                    'harga' => $barangData['harga'] ?? null,
                                    'total' => $barangData['total'],
                                    'is_active' => true,
                                ]);
                            }
                        }
                    }
                }
                
                // Handle tindakans if provided
                if ($request->has('tindakans') && is_array($request->tindakans)) {
                    foreach ($request->tindakans as $tindakanData) {
                        PerawatanTindakan::create([
                            'perawatan_id' => $perawatan->id,
                            'treatment_id' => $tindakanData['treatment_id'],
                            'tanggal' => $tindakanData['tanggal'] ?? now(),
                            'jumlah' => $tindakanData['jumlah'],
                            'beautician_id' => $tindakanData['beautician_id'] ?? null,
                            'harga' => $tindakanData['harga'],
                            'diskon' => $tindakanData['diskon'] ?? null,
                            'rp_percent' => $tindakanData['rp_percent'] ?? null,
                            'total' => $tindakanData['total'],
                            'status' => $tindakanData['status'] ?? 'draft',
                            'catatan' => $tindakanData['catatan'] ?? null,
                            'is_active' => true,
                        ]);
                    }
                }
                
                DB::commit();
                
                return response()->json($perawatan->load(['pasien', 'treatment', 'staff', 'antrian', 'reseps.barangs', 'tindakans.treatment', 'tindakans.beautician']), 201);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message' => 'Gagal membuat perawatan: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perawatan = Perawatan::with(['pasien', 'treatment', 'staff', 'antrian', 'reseps.barangs', 'tindakans.treatment', 'tindakans.beautician'])->findOrFail($id);
        return response()->json($perawatan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PerawatanRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $perawatan = Perawatan::findOrFail($id);
            $validated = $request->validated();
            
            // Extract nested data before updating perawatan
            $reseps = $validated['reseps'] ?? null;
            $tindakans = $validated['tindakans'] ?? null;
            unset($validated['reseps'], $validated['tindakans']);
            
            $perawatan->update($validated);
            
            // Handle reseps update (delete existing and create new)
            if ($reseps !== null) {
                // Delete existing reseps and their barangs (cascade)
                $perawatan->reseps()->delete();
                
                foreach ($reseps as $resepData) {
                    $resep = PerawatanResep::create([
                        'perawatan_id' => $perawatan->id,
                        'antrian_id' => $resepData['antrian_id'],
                        'pasien_id' => $perawatan->pasien_id,
                        'staff_id' => $perawatan->staff_id,
                        'kode' => Generator::generateID('RES'),
                        'tanggal' => $resepData['tanggal'] ?? now(),
                        'status' => $resepData['status'] ?? 'draft',
                        'is_active' => true,
                    ]);
                    
                    if (isset($resepData['barangs']) && is_array($resepData['barangs'])) {
                        foreach ($resepData['barangs'] as $barangData) {
                            PerawatanResepBarang::create([
                                'perawatan_resep_id' => $resep->id,
                                'barang_id' => $barangData['barang_id'],
                                'kode_barang' => $barangData['kode_barang'],
                                'nama_barang' => $barangData['nama_barang'],
                                'jumlah' => $barangData['jumlah'],
                                'unit' => $barangData['unit'],
                                'harga' => $barangData['harga'] ?? null,
                                'total' => $barangData['total'],
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }
            
            // Handle tindakans update (delete existing and create new)
            if ($tindakans !== null) {
                $perawatan->tindakans()->delete();
                
                foreach ($tindakans as $tindakanData) {
                    PerawatanTindakan::create([
                        'perawatan_id' => $perawatan->id,
                        'treatment_id' => $tindakanData['treatment_id'],
                        'tanggal' => $tindakanData['tanggal'] ?? now(),
                        'jumlah' => $tindakanData['jumlah'],
                        'beautician_id' => $tindakanData['beautician_id'] ?? null,
                        'harga' => $tindakanData['harga'],
                        'diskon' => $tindakanData['diskon'] ?? null,
                        'rp_percent' => $tindakanData['rp_percent'] ?? null,
                        'total' => $tindakanData['total'],
                        'status' => $tindakanData['status'] ?? 'draft',
                        'catatan' => $tindakanData['catatan'] ?? null,
                        'is_active' => true,
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json($perawatan->load(['pasien', 'treatment', 'staff', 'antrian', 'reseps.barangs', 'tindakans.treatment', 'tindakans.beautician']));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal mengupdate perawatan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perawatan = Perawatan::findOrFail($id);
        $perawatan->delete();
        return response()->json(null, 204);
    }

    /**
     * Update treatment status
     */
    public function updateStatus()
    {
        $request = request();
        $request->validate([
            'status' => 'required|in:planned,in_progress,completed,cancelled',
        ]);

        $perawatan = Perawatan::findOrFail($request->route('perawatan'));
        $perawatan->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Treatment status updated successfully',
            'perawatan' => $perawatan
        ]);
    }
}
