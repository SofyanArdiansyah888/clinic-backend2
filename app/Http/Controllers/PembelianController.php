<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembelianBarangRequest;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Pembelian::with(['supplier', 'staff']);
        
        if (request()->has('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        if (request()->has('supplier_id')) {
            $query->where('supplier_id', request('supplier_id'));
        }
        
        $pembelians = $query->orderBy('tanggal', 'desc')->get();
        return response()->json($pembelians);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PembelianBarangRequest $request)
    {
        DB::beginTransaction();
        try {
            $pembelian = Pembelian::create([
                'supplier_id' => $request->supplier_id,
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
                $subtotal = $detail['qty'] * $detail['harga_beli'];
                $totalHarga += $subtotal;
                
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $detail['barang_id'],
                    'qty' => $detail['qty'],
                    'harga_beli' => $detail['harga_beli'],
                    'subtotal' => $subtotal,
                    'is_active' => true,
                ]);
            }

            $pembelian->update(['total_harga' => $totalHarga]);
            
            DB::commit();
            
            $pembelian->load(['supplier', 'staff', 'details.barang']);
            return response()->json($pembelian, 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to create purchase'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pembelian = Pembelian::with(['supplier', 'staff', 'details.barang'])->findOrFail($id);
        return response()->json($pembelian);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PembelianBarangRequest $request, string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->update($request->validated());
        
        return response()->json($pembelian);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->delete();
        return response()->json(null, 204);
    }

    /**
     * Update purchase status
     */
    public function updateStatus()
    {
        $request = request();
        $request->validate([
            'status' => 'required|in:draft,ordered,received,cancelled',
        ]);

        $pembelian = Pembelian::findOrFail($request->route('pembelian'));
        $pembelian->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Purchase status updated successfully',
            'pembelian' => $pembelian
        ]);
    }
}
