<?php

namespace App\Http\Controllers;

use App\Http\Requests\KonversiBarangRequest;
use App\Models\KonversiStok;
use App\Models\KonversiStokDetail;
use App\Models\Barang;
use App\Models\KartuStok;
use App\Utils\Generator;
use Illuminate\Support\Facades\DB;

class KonversiStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = KonversiStok::with('details.barang');
        
        if (request()->has('search') && request('search')) {
            $query->where(function($q) {
                $q->where('kode', 'like', '%' . request('search') . '%')
                  ->orWhere('keterangan', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request()->has('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        $konversiStoks = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'data' => $konversiStoks,
            'total' => $konversiStoks->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KonversiBarangRequest $request)
    {

        DB::beginTransaction();
        try {
            $konversiStok = KonversiStok::create([
                'kode' => Generator::generateID('KVS'),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan ?? 'Konversi Stok',
                'is_active' => true,
            ]);

            foreach ($request->details as $detail) {
                $konversiStokDetail = KonversiStokDetail::create([
                    'kode' => Generator::generateID('KVD'),
                    'konversi_stok_id' => $konversiStok->id,
                    'barang_id' => $detail['barang_id'],
                    'qty' => $detail['qty'],
                    'tipe' => $detail['tipe'],
                    'is_active' => true,
                ]);

                // Update stock and create kartu stok
                $barang = Barang::find($detail['barang_id']);
                if ($barang) {
                    // Get last saldo for this barang
                    $lastKartuStok = KartuStok::where('barang_id', $detail['barang_id'])
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

                    $saldoAwal = $lastKartuStok ? $lastKartuStok->saldo : ($barang->stok_aktual ?? 0);
                    
                    if ($detail['tipe'] === 'input') {
                        $qtyMasuk = $detail['qty'];
                        $qtyKeluar = 0;
                        $saldo = $saldoAwal + $qtyMasuk;
                        $barang->stok_aktual = $saldo;
                    } else {
                        $qtyMasuk = 0;
                        $qtyKeluar = $detail['qty'];
                        $saldo = $saldoAwal - $qtyKeluar;
                        $barang->stok_aktual = $saldo;
                    }
                    
                    // Create kartu stok entry
                    $keteranganDetail = isset($detail['keterangan']) && !empty($detail['keterangan']) ? ' - ' . $detail['keterangan'] : '';
                    KartuStok::create([
                        'kode' => Generator::generateID('KST'),
                        'barang_id' => $detail['barang_id'],
                        'tanggal' => $request->tanggal,
                        'keterangan' => 'Konversi Stok - ' . ($request->keterangan ?? 'Konversi Stok') . $keteranganDetail,
                        'qty_masuk' => $qtyMasuk,
                        'qty_keluar' => $qtyKeluar,
                        'saldo' => $saldo,
                        'referensi' => $konversiStok->kode,
                        'is_active' => true,
                    ]);
                    
                    $barang->save();
                }
            }

            DB::commit();
            return response()->json($konversiStok->load('details.barang'), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating conversion: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $konversiStok = KonversiStok::with('details.barang')->find($id);
        if (!$konversiStok) {
            return response()->json(['message' => 'Conversion not found'], 404);
        }
        return response()->json($konversiStok);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KonversiBarangRequest $request, string $id)
    {
        $konversiStok = KonversiStok::find($id);
        if (!$konversiStok) {
            return response()->json(['message' => 'Conversion not found'], 404);
        }

        $konversiStok->update($request->validated());
        return response()->json($konversiStok->load('details.barang'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $konversiStok = KonversiStok::find($id);
        if (!$konversiStok) {
            return response()->json(['message' => 'Conversion not found'], 404);
        }

        $konversiStok->is_active = false;
        $konversiStok->save();
        return response()->json(['message' => 'Conversion deactivated successfully']);
    }
}
