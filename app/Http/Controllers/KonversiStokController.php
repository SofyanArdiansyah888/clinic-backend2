<?php

namespace App\Http\Controllers;

use App\Http\Requests\KonversiBarangRequest;
use App\Models\KonversiStok;
use App\Models\KonversiStokDetail;
use App\Models\Barang;
use App\Utils\Generator;
use Illuminate\Support\Facades\DB;

class KonversiStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $konversiStoks = KonversiStok::with('details.barang')->get();
        return response()->json($konversiStoks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KonversiBarangRequest $request)
    {

        DB::beginTransaction();
        try {
            $konversiStok = new KonversiStok([
                'id' => Generator::generateID('KVS'),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'is_active' => true,
            ]);
            $konversiStok->save();

            foreach ($request->details as $detail) {
                $konversiStokDetail = new KonversiStokDetail([
                    'id' => Generator::generateID('KVD'),
                    'konversi_stok_id' => $konversiStok->id,
                    'barang_id' => $detail['barang_id'],
                    'qty' => $detail['qty'],
                    'tipe' => $detail['tipe'],
                    'is_active' => true,
                ]);
                $konversiStokDetail->save();

                // Update stock
                $barang = Barang::find($detail['barang_id']);
                if ($detail['tipe'] === 'input') {
                    $barang->stok += $detail['qty'];
                } else {
                    $barang->stok -= $detail['qty'];
                }
                $barang->save();
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
