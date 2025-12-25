<?php

namespace App\Http\Controllers;

use App\Http\Requests\KartuStokRequest;
use App\Models\KartuStok;
use App\Models\Barang;
use App\Utils\Generator;
use Illuminate\Http\Request;

class KartuStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KartuStok::with('barang');
        
        if ($request->has('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }
        
        if ($request->has('tanggal_dari')) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->has('tanggal_sampai')) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }
        
        // Filter berdasarkan lokasi barang (apotek/gudang)
        if ($request->has('lokasi_barang')) {
            $lokasi = $request->lokasi_barang;
            $query->whereHas('barang', function($q) use ($lokasi) {
                $q->where('lokasi_barang', $lokasi);
            });
        }
        
        $kartuStoks = $query->orderBy('tanggal', 'asc')->get();
        return response()->json($kartuStoks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KartuStokRequest $request)
    {

        // Get last saldo for this barang
        $lastKartuStok = KartuStok::where('barang_id', $request->barang_id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $saldoAwal = $lastKartuStok ? $lastKartuStok->saldo : 0;
        $qtyMasuk = $request->qty_masuk ?? 0;
        $qtyKeluar = $request->qty_keluar ?? 0;
        $saldo = $saldoAwal + $qtyMasuk - $qtyKeluar;

        $kartuStok = new KartuStok([
            'id' => Generator::generateID('KST'),
            'barang_id' => $request->barang_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'qty_masuk' => $qtyMasuk,
            'qty_keluar' => $qtyKeluar,
            'saldo' => $saldo,
            'referensi' => $request->referensi,
            'is_active' => true,
        ]);
        $kartuStok->save();

        return response()->json($kartuStok->load('barang'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kartuStok = KartuStok::with('barang')->find($id);
        if (!$kartuStok) {
            return response()->json(['message' => 'Stock card not found'], 404);
        }
        return response()->json($kartuStok);
    }

    /**
     * Get stock card for specific barang.
     */
    public function getByBarang(string $barangId)
    {
        $kartuStoks = KartuStok::where('barang_id', $barangId)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        
        return response()->json($kartuStoks);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kartuStok = KartuStok::find($id);
        if (!$kartuStok) {
            return response()->json(['message' => 'Stock card not found'], 404);
        }

        $kartuStok->is_active = false;
        $kartuStok->save();
        return response()->json(['message' => 'Stock card deactivated successfully']);
    }
}
