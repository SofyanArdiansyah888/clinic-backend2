<?php

namespace App\Http\Controllers;

use App\Http\Requests\TreatmentRequest;
use App\Models\Treatment;

class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Treatment::query();
        
        if (request()->has('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('kategori')) {
            $query->where('kategori', request('kategori'));
        }
        
        if (request()->has('is_active')) {
            $query->where('is_active', request('is_active'));
        }
        
        $treatments = $query->orderBy('nama', 'asc')->get();
        return response()->json($treatments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TreatmentRequest $request)
    {
        $treatment = Treatment::create($request->validated());
        return response()->json($treatment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $treatment = Treatment::findOrFail($id);
        return response()->json($treatment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TreatmentRequest $request, string $id)
    {
        $treatment = Treatment::findOrFail($id);
        $treatment->update($request->validated());
        return response()->json($treatment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $treatment = Treatment::findOrFail($id);
        $treatment->delete();
        return response()->json(null, 204);
    }
}
