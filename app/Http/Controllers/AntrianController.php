<?php

namespace App\Http\Controllers;

use App\Http\Requests\AntrianRequest;
use App\Models\Antrian;
use App\Utils\Generator;

class AntrianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $antrians = Antrian::with('pasien')->get();
        return response()->json($antrians);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AntrianRequest $request)
    {
        $antrian = new Antrian([
            'id' => Generator::generateID('ANT'),
            'pasien_id' => $request->pasien_id,
            'nomor_antrian' => $request->nomor_antrian,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'is_active' => true,
        ]);
        $antrian->save();

        return response()->json($antrian->load('pasien'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $antrian = Antrian::with('pasien')->find($id);
        if (!$antrian) {
            return response()->json(['message' => 'Queue not found'], 404);
        }
        return response()->json($antrian);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AntrianRequest $request, string $id)
    {
        $antrian = Antrian::find($id);
        if (!$antrian) {
            return response()->json(['message' => 'Queue not found'], 404);
        }

        $antrian->update($request->validated());
        return response()->json($antrian->load('pasien'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $antrian = Antrian::find($id);
        if (!$antrian) {
            return response()->json(['message' => 'Queue not found'], 404);
        }

        $antrian->is_active = false;
        $antrian->save();
        return response()->json(['message' => 'Queue deactivated successfully']);
    }
}
