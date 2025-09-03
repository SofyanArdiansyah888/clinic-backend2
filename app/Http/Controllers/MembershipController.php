<?php

namespace App\Http\Controllers;

use App\Http\Requests\MembershipRequest;
use App\Models\Membership;
use App\Models\Pasien;
use App\Utils\Generator;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Membership::with('pasien');
        
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('status', 'active')
                      ->where('tanggal_mulai', '<=', now())
                      ->where('tanggal_selesai', '>=', now());
            } elseif ($request->status === 'inactive') {
                $query->where(function($q) {
                    $q->where('is_active', false)
                      ->orWhere('status', '!=', 'active')
                      ->orWhere('tanggal_mulai', '>', now())
                      ->orWhere('tanggal_selesai', '<', now());
                });
            }
        }
        
        if ($request->has('pasien_id')) {
            $query->where('pasien_id', $request->pasien_id);
        }
        
        $memberships = $query->get();
        return response()->json($memberships);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MembershipRequest $request)
    {
        $membership = new Membership([
            'id' => Generator::generateID('MEM'),
            'pasien_id' => $request->pasien_id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'status' => $request->status,
            'biaya' => $request->biaya,
            'diskon' => $request->diskon,
            'is_active' => true,
        ]);
        $membership->save();

        return response()->json($membership->load('pasien'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $membership = Membership::with('pasien')->find($id);
        if (!$membership) {
            return response()->json(['message' => 'Membership not found'], 404);
        }
        return response()->json($membership);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MembershipRequest $request, string $id)
    {
        $membership = Membership::find($id);
        if (!$membership) {
            return response()->json(['message' => 'Membership not found'], 404);
        }

        $membership->update($request->validated());
        return response()->json($membership->load('pasien'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $membership = Membership::find($id);
        if (!$membership) {
            return response()->json(['message' => 'Membership not found'], 404);
        }

        $membership->is_active = false;
        $membership->save();
        return response()->json(['message' => 'Membership deactivated successfully']);
    }

    /**
     * Add points to membership.
     */
    public function addPoints(Request $request, string $id)
    {
        $membership = Membership::find($id);
        if (!$membership) {
            return response()->json(['message' => 'Membership not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'poin' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $membership->addPoints($request->poin);
        return response()->json(['message' => 'Points added successfully', 'membership' => $membership->load('pasien')]);
    }

    /**
     * Use points from membership.
     */
    public function usePoints(Request $request, string $id)
    {
        $membership = Membership::find($id);
        if (!$membership) {
            return response()->json(['message' => 'Membership not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'poin' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($membership->usePoints($request->poin)) {
            return response()->json(['message' => 'Points used successfully', 'membership' => $membership->load('pasien')]);
        } else {
            return response()->json(['message' => 'Insufficient points'], 400);
        }
    }
}
