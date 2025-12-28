<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerawatanRequest;
use App\Models\Perawatan;
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
        $query = Perawatan::with(['pasien', 'treatment', 'staff']);
        
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
                    
                    $perawatans[] = $perawatan->load(['pasien', 'treatment', 'staff']);
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
            // Old format (single perawatan)
            $validated = $request->validated();
            $validated['kode'] = Generator::generateID('PRW');
            $validated['is_active'] = $validated['is_active'] ?? true;
            
            $perawatan = Perawatan::create($validated);
            return response()->json($perawatan->load(['pasien', 'treatment', 'staff']), 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perawatan = Perawatan::with(['pasien', 'treatment', 'staff'])->findOrFail($id);
        return response()->json($perawatan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PerawatanRequest $request, string $id)
    {
        $perawatan = Perawatan::findOrFail($id);
        $perawatan->update($request->validated());
        
        return response()->json($perawatan);
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
