<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerusahaanRequest;
use App\Models\Perusahaan;
use App\Utils\Generator;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perusahaans = Perusahaan::all();
        return response()->json($perusahaans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PerusahaanRequest $request)
    {
        $perusahaan = new Perusahaan([
            'id' => Generator::generateID('PRS'),
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'website' => $request->website,
            'npwp' => $request->npwp,
            'is_active' => true,
        ]);
        $perusahaan->save();

        return response()->json($perusahaan, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perusahaan = Perusahaan::find($id);
        if (!$perusahaan) {
            return response()->json(['message' => 'Company not found'], 404);
        }
        return response()->json($perusahaan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PerusahaanRequest $request, string $id)
    {
        $perusahaan = Perusahaan::find($id);
        if (!$perusahaan) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $perusahaan->update($request->validated());
        return response()->json($perusahaan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perusahaan = Perusahaan::find($id);
        if (!$perusahaan) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $perusahaan->is_active = false;
        $perusahaan->save();
        return response()->json(['message' => 'Company deactivated successfully']);
    }
}
