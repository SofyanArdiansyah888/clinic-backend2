<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Staff;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Staff::query();
        
        if (request()->has('nama')) {
            $query->where('nama', 'like', '%' . request('nama') . '%');
        }
        
        if (request()->has('jabatan')) {
            $query->where('jabatan', request('jabatan'));
        }
        
        if (request()->has('departemen')) {
            $query->where('departemen', request('departemen'));
        }
        
        if (request()->has('is_active')) {
            $query->where('is_active', request('is_active'));
        }
        
        $staffs = $query->orderBy('nama', 'asc')->get();
        return response()->json($staffs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffRequest $request)
    {
        $staff = Staff::create($request->validated());
        return response()->json($staff, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $staff = Staff::findOrFail($id);
        return response()->json($staff);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffRequest $request, string $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->update($request->validated());
        return response()->json($staff);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return response()->json(null, 204);
    }
}
