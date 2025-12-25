<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProduksiBarangRequest;
use App\Models\ProduksiBarang;
use App\Models\ProduksiBarangDetail;
use App\Models\Barang;
use App\Models\KartuStok;
use App\Utils\Generator;
use Illuminate\Support\Facades\DB;

class ProduksiBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ProduksiBarang::with('details.barang');
        
        if (request()->has('search') && request('search')) {
            $query->where(function($q) {
                $q->where('kode', 'like', '%' . request('search') . '%')
                  ->orWhere('keterangan', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request()->has('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        $produksiBarangs = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'data' => $produksiBarangs,
            'total' => $produksiBarangs->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProduksiBarangRequest $request)
    {

        DB::beginTransaction();
        try {
            $produksiBarang = ProduksiBarang::create([
                'kode' => Generator::generateID('PRB'),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan ?? 'Produksi Barang',
                'is_active' => true,
            ]);

            foreach ($request->details as $detail) {
                $produksiBarangDetail = ProduksiBarangDetail::create([
                    'kode' => Generator::generateID('PRD'),
                    'produksi_barang_id' => $produksiBarang->id,
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
                        'keterangan' => 'Produksi Barang - ' . ($request->keterangan ?? 'Produksi Barang') . $keteranganDetail,
                        'qty_masuk' => $qtyMasuk,
                        'qty_keluar' => $qtyKeluar,
                        'saldo' => $saldo,
                        'referensi' => $produksiBarang->kode,
                        'is_active' => true,
                    ]);
                    
                    $barang->save();
                }
            }

            DB::commit();
            return response()->json($produksiBarang->load('details.barang'), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating production: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produksiBarang = ProduksiBarang::with('details.barang')->find($id);
        if (!$produksiBarang) {
            return response()->json(['message' => 'Production not found'], 404);
        }
        return response()->json($produksiBarang);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProduksiBarangRequest $request, string $id)
    {
        $produksiBarang = ProduksiBarang::find($id);
        if (!$produksiBarang) {
            return response()->json(['message' => 'Production not found'], 404);
        }

        $produksiBarang->update($request->validated());
        return response()->json($produksiBarang->load('details.barang'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produksiBarang = ProduksiBarang::find($id);
        if (!$produksiBarang) {
            return response()->json(['message' => 'Production not found'], 404);
        }

        $produksiBarang->is_active = false;
        $produksiBarang->save();
        return response()->json(['message' => 'Production deactivated successfully']);
    }
}
