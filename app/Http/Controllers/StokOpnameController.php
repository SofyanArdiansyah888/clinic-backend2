<?php

namespace App\Http\Controllers;

use App\Http\Requests\StokOpnameRequest;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\Barang;
use App\Models\KartuStok;
use App\Utils\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokOpname::with('details.barang');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $stokOpnames = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'data' => $stokOpnames,
            'total' => $stokOpnames->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StokOpnameRequest $request)
    {
        DB::beginTransaction();
        try {
            $stokOpname = StokOpname::create([
                'kode' => Generator::generateID('STO'),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan ?? 'Stok Opname',
                'is_active' => true,
            ]);

            foreach ($request->details as $detail) {
                $barang = Barang::find($detail['barang_id']);
                if (!$barang) {
                    continue;
                }

                $stokSistem = $barang->stok_aktual ?? 0;
                $stokFisik = $detail['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                $stokOpnameDetail = StokOpnameDetail::create([
                    'kode' => Generator::generateID('STD'),
                    'stok_opname_id' => $stokOpname->id,
                    'barang_id' => $detail['barang_id'],
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $detail['keterangan'] ?? null,
                    'is_active' => true,
                ]);

                // Get last saldo for this barang
                $lastKartuStok = KartuStok::where('barang_id', $detail['barang_id'])
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $saldoAwal = $lastKartuStok ? $lastKartuStok->saldo : $stokSistem;
                $saldo = $stokFisik; // New stock after opname

                // Create kartu stok entry
                if ($selisih != 0) {
                    $qtyMasuk = $selisih > 0 ? $selisih : 0;
                    $qtyKeluar = $selisih < 0 ? abs($selisih) : 0;

                    KartuStok::create([
                        'kode' => Generator::generateID('KST'),
                        'barang_id' => $detail['barang_id'],
                        'tanggal' => $request->tanggal,
                        'keterangan' => 'Stok Opname - ' . ($request->keterangan ?? 'Penyesuaian Stok'),
                        'qty_masuk' => $qtyMasuk,
                        'qty_keluar' => $qtyKeluar,
                        'saldo' => $saldo,
                        'referensi' => $stokOpname->kode,
                        'is_active' => true,
                    ]);
                }

                // Update stock to match physical count
                $barang->stok_aktual = $stokFisik;
                $barang->save();
            }

            DB::commit();
            return response()->json($stokOpname->load('details.barang'), 201);
        } catch (\Exception $e) {
            DB::rollback();
            $errorMessage = 'Error creating stock opname';
            if (config('app.debug')) {
                $errorMessage .= ': ' . $e->getMessage();
            }
            return response()->json([
                'message' => $errorMessage,
                'error' => config('app.debug') ? $e->getMessage() : null,
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stokOpname = StokOpname::with('details.barang')->find($id);
        if (!$stokOpname) {
            return response()->json(['message' => 'Stock opname not found'], 404);
        }
        return response()->json($stokOpname);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StokOpnameRequest $request, string $id)
    {
        $stokOpname = StokOpname::find($id);
        if (!$stokOpname) {
            return response()->json(['message' => 'Stock opname not found'], 404);
        }

        $stokOpname->update($request->validated());
        return response()->json($stokOpname->load('details.barang'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stokOpname = StokOpname::find($id);
        if (!$stokOpname) {
            return response()->json(['message' => 'Stock opname not found'], 404);
        }

        $stokOpname->is_active = false;
        $stokOpname->save();
        return response()->json(['message' => 'Stock opname deactivated successfully']);
    }
}
