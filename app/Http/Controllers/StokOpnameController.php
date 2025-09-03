<?php

namespace App\Http\Controllers;

use App\Http\Requests\StokOpnameRequest;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\Barang;
use App\Utils\Generator;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stokOpnames = StokOpname::with('details.barang')->get();
        return response()->json($stokOpnames);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StokOpnameRequest $request)
    {

        DB::beginTransaction();
        try {
            $stokOpname = new StokOpname([
                'id' => Generator::generateID('STO'),
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'is_active' => true,
            ]);
            $stokOpname->save();

            foreach ($request->details as $detail) {
                $barang = Barang::find($detail['barang_id']);
                $selisih = $detail['stok_fisik'] - $barang->stok;

                $stokOpnameDetail = new StokOpnameDetail([
                    'id' => Generator::generateID('STD'),
                    'stok_opname_id' => $stokOpname->id,
                    'barang_id' => $detail['barang_id'],
                    'stok_sistem' => $barang->stok,
                    'stok_fisik' => $detail['stok_fisik'],
                    'selisih' => $selisih,
                    'keterangan' => $detail['keterangan'] ?? null,
                    'is_active' => true,
                ]);
                $stokOpnameDetail->save();

                // Update stock to match physical count
                $barang->stok = $detail['stok_fisik'];
                $barang->save();
            }

            DB::commit();
            return response()->json($stokOpname->load('details.barang'), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating stock opname: ' . $e->getMessage()], 500);
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
