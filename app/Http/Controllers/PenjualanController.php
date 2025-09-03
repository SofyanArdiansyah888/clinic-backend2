<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenjualanBarangRequest;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Penjualan::with(['pasien', 'staff']);
        
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('pasien_id')) {
            $query->where('pasien_id', $request->pasien_id);
        }
        
        $penjualans = $query->orderBy('tanggal', 'desc')->get();
        return response()->json($penjualans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PenjualanBarangRequest $request): JsonResponse
    {

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'pasien_id' => $request->pasien_id,
                'staff_id' => $request->staff_id,
                'tanggal' => $request->tanggal,
                'no_invoice' => $request->no_invoice,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'total_harga' => 0,
                'is_active' => true,
            ]);

            $totalHarga = 0;
            foreach ($request->details as $detail) {
                $subtotal = $detail['qty'] * $detail['harga_jual'];
                $totalHarga += $subtotal;
                
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $detail['barang_id'],
                    'qty' => $detail['qty'],
                    'harga_jual' => $detail['harga_jual'],
                    'subtotal' => $subtotal,
                    'is_active' => true,
                ]);
            }

            $penjualan->update(['total_harga' => $totalHarga]);
            
            DB::commit();
            
            $penjualan->load(['pasien', 'staff', 'details.barang']);
            return response()->json($penjualan, 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to create sale'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $penjualan = Penjualan::with(['pasien', 'staff', 'details.barang'])->findOrFail($id);
        return response()->json($penjualan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PenjualanBarangRequest $request, string $id): JsonResponse
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update($request->validated());
        
        return response()->json($penjualan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();
        return response()->json(null, 204);
    }

    /**
     * Update sale status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:draft,confirmed,completed,cancelled',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Sale status updated successfully',
            'penjualan' => $penjualan
        ]);
    }
}
