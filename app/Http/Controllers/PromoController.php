<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoRequest;
use App\Models\Promo;
use App\Utils\Generator;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Promo::query();
        
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('tanggal_mulai', '<=', now())
                      ->where('tanggal_selesai', '>=', now());
            } elseif ($request->status === 'inactive') {
                $query->where(function($q) {
                    $q->where('is_active', false)
                      ->orWhere('tanggal_mulai', '>', now())
                      ->orWhere('tanggal_selesai', '<', now());
                });
            }
        }
        
        $promos = $query->get();
        return response()->json($promos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromoRequest $request)
    {
        $promo = new Promo([
            'id' => Generator::generateID('PRM'),
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'nilai' => $request->nilai,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'minimal_pembelian' => $request->minimal_pembelian,
            'maksimal_diskon' => $request->maksimal_diskon,
            'is_active' => true,
        ]);
        $promo->save();

        return response()->json($promo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promo = Promo::find($id);
        if (!$promo) {
            return response()->json(['message' => 'Promo not found'], 404);
        }
        return response()->json($promo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromoRequest $request, string $id)
    {
        $promo = Promo::find($id);
        if (!$promo) {
            return response()->json(['message' => 'Promo not found'], 404);
        }

        $promo->update($request->validated());
        
        return response()->json($promo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promo = Promo::find($id);
        if (!$promo) {
            return response()->json(['message' => 'Promo not found'], 404);
        }

        $promo->is_active = false;
        $promo->save();
        return response()->json(['message' => 'Promo deactivated successfully']);
    }

    /**
     * Validate promo for transaction.
     */
    public function validatePromo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promo_id' => 'required|exists:promos,id',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $promo = Promo::find($request->promo_id);
        $discount = $promo->calculateDiscount($request->subtotal);

        return response()->json([
            'promo' => $promo,
            'subtotal' => $request->subtotal,
            'discount' => $discount,
            'total_after_discount' => $request->subtotal - $discount,
        ]);
    }
}
