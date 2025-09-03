<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerawatanRequest;
use App\Models\Perawatan;

class PerawatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Perawatan::with(['pasien', 'treatment', 'staff']);
        
        if (request()->has('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        if (request()->has('pasien_id')) {
            $query->where('pasien_id', request('pasien_id'));
        }
        
        if (request()->has('staff_id')) {
            $query->where('staff_id', request('staff_id'));
        }
        
        $perawatans = $query->orderBy('tanggal', 'desc')
                            ->orderBy('jam_mulai', 'asc')
                            ->get();
        
        return response()->json($perawatans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PerawatanRequest $request)
    {
        $perawatan = Perawatan::create($request->validated());
        return response()->json($perawatan, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perawatan = Perawatan::with(['pasien', 'treatment', 'staff'])->findOrFail($id);
        return response()->json($perawatan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PerawatanRequest $request, string $id)
    {
        $perawatan = Perawatan::findOrFail($id);
        $perawatan->update($request->validated());
        
        return response()->json($perawatan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perawatan = Perawatan::findOrFail($id);
        $perawatan->delete();
        return response()->json(null, 204);
    }

    /**
     * Update treatment status
     */
    public function updateStatus()
    {
        $request = request();
        $request->validate([
            'status' => 'required|in:planned,in_progress,completed,cancelled',
        ]);

        $perawatan = Perawatan::findOrFail($request->route('perawatan'));
        $perawatan->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Treatment status updated successfully',
            'perawatan' => $perawatan
        ]);
    }
}
