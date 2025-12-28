<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Utils\Generator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Appointment::with(['pasien', 'staff']);
        
        if (request()->has('tanggal') && request('tanggal')) {
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

        // Search functionality
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhereHas('pasien', function($p) use ($search) {
                      $p->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('kode', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('staff', function($s) use ($search) {
                      $s->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Pagination
        $perPage = request('per_page', request('page_size', 10));
        $page = request('page', 1);
        
        $appointments = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam', 'asc')
                             ->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $appointments->map(function($appointment) {
            return [
                'id' => $appointment->id,
                'kode' => $appointment->kode,
                'pasien_id' => $appointment->pasien_id,
                'pasien' => $appointment->pasien ? [
                    'id' => $appointment->pasien->id,
                    'kode' => $appointment->pasien->kode,
                    'nama' => $appointment->pasien->nama,
                    'alamat' => $appointment->pasien->alamat ?? null,
                    'no_telp' => $appointment->pasien->no_telp ?? null,
                    'email' => $appointment->pasien->email ?? null,
                ] : null,
                'staff_id' => $appointment->staff_id,
                'staff' => $appointment->staff ? [
                    'id' => $appointment->staff->id,
                    'kode' => $appointment->staff->kode,
                    'nama' => $appointment->staff->nama,
                    'nip' => $appointment->staff->nip,
                    'jabatan' => $appointment->staff->jabatan,
                ] : null,
                'tanggal' => $appointment->tanggal ? $appointment->tanggal->format('Y-m-d') : null,
                'jam' => $appointment->jam,
                'status' => $appointment->status,
                'keterangan' => $appointment->keterangan,
                'is_active' => $appointment->is_active,
                'created_at' => $appointment->created_at,
                'updated_at' => $appointment->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $appointments->currentPage(),
            'page_size' => $appointments->perPage(),
            'total_pages' => $appointments->lastPage(),
            'total_rows' => $appointments->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $appointment = Appointment::create([
            'kode' => Generator::generateID('APT'),
            'pasien_id' => $request->pasien_id,
            'staff_id' => $request->staff_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => $request->status ?? 'scheduled',
            'keterangan' => $request->keterangan,
            'is_active' => true,
        ]);

        return response()->json($appointment->load(['pasien', 'staff']), 201);
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
