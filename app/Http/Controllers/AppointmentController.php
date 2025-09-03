<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Appointment::with(['pasien', 'staff']);
        
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
        
        $appointments = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam', 'asc')
                             ->get();
        
        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $appointment = Appointment::create([
            'pasien_id' => $request->pasien_id,
            'staff_id' => $request->staff_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'scheduled',
            'keterangan' => $request->keterangan,
            'is_active' => true,
        ]);

        return response()->json($appointment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::with(['pasien', 'staff'])->findOrFail($id);
        return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->validated());
        
        return response()->json($appointment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(null, 204);
    }

    /**
     * Update appointment status
     */
    public function updateStatus()
    {
        $request = request();
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled',
        ]);

        $appointment = Appointment::findOrFail($request->route('appointment'));
        $appointment->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Appointment status updated successfully',
            'appointment' => $appointment
        ]);
    }
}
