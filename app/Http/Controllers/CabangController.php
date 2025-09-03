<?php

namespace App\Http\Controllers;

use App\Http\Requests\CabangRequest;
use App\Models\Cabang;
use App\Utils\Generator;

class CabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cabangs = Cabang::with(['staff', 'appointments'])->get();
        return response()->json($cabangs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CabangRequest $request)
    {
        $cabang = new Cabang([
            'id' => Generator::generateID('CAB'),
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'kepala_cabang' => $request->kepala_cabang,
            'is_active' => true,
        ]);
        $cabang->save();

        return response()->json($cabang, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cabang = Cabang::with(['staff', 'appointments'])->find($id);
        if (!$cabang) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        return response()->json($cabang);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CabangRequest $request, string $id)
    {
        $cabang = Cabang::find($id);
        if (!$cabang) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $cabang->update($request->validated());
        return response()->json($cabang->load(['staff', 'appointments']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cabang = Cabang::find($id);
        if (!$cabang) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $cabang->is_active = false;
        $cabang->save();
        return response()->json(['message' => 'Branch deactivated successfully']);
    }
}
