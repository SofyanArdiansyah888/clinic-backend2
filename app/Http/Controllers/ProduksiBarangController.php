<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProduksiBarangRequest;
use App\Models\ProduksiBarang;
use App\Models\ProduksiBarangDetail;
use App\Models\Barang;
use App\Utils\Generator;
use Illuminate\Support\Facades\DB;

class ProduksiBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produksiBarangs = ProduksiBarang::with('details.barang')->get();
        return response()->json($produksiBarangs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProduksiBarangRequest $request)
    {

        DB::beginTransaction();
        try {
            $produksiBarang = new ProduksiBarang([
                'id' => Generator::generateID('PRD'),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'is_active' => true,
            ]);
            $produksiBarang->save();

            foreach ($request->details as $detail) {
                $produksiBarangDetail = new ProduksiBarangDetail([
                    'id' => Generator::generateID('PRD'),
                    'produksi_barang_id' => $produksiBarang->id,
                    'barang_id' => $detail['barang_id'],
                    'qty' => $detail['qty'],
                    'tipe' => $detail['tipe'],
                    'is_active' => true,
                ]);
                $produksiBarangDetail->save();

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
