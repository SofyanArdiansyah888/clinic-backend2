<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Barang::query();
        
        if (request()->has('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('kategori')) {
            $query->where('kategori', request('kategori'));
        }
        
        if (request()->has('is_active')) {
            $query->where('is_active', request('is_active'));
        }
        
        $barangs = $query->orderBy('nama', 'asc')->get();
        return response()->json($barangs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BarangRequest $request)
    {
        $barang = Barang::create($request->validated());
        return response()->json($barang, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barang = Barang::findOrFail($id);
        return response()->json($barang);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BarangRequest $request, string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->update($request->validated());
        return response()->json($barang);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return response()->json(null, 204);
    }

    /**
     * Update stock quantity
     */
    public function updateStock()
    {
        $request = request();
        $request->validate([
            'stok_aktual' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($request->route('barang'));
        $barang->update(['stok_aktual' => $request->stok_aktual]);
        
        return response()->json([
            'message' => 'Stock updated successfully',
            'barang' => $barang
        ]);
    }
}
