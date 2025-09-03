<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasienRequest;
use App\Models\Pasien;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Pasien::query();
        
        if (request()->has('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('is_active')) {
            $query->where('is_active', request('is_active'));
        }
        
        $pasiens = $query->orderBy('created_at', 'desc')->get();
        return response()->json($pasiens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PasienRequest $request)
    {
        $pasien = Pasien::create($request->validated());
        return response()->json($pasien, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        return response()->json($pasien);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PasienRequest $request, string $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->update($request->validated());
        return response()->json($pasien);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();
        return response()->json(null, 204);
    }
}
