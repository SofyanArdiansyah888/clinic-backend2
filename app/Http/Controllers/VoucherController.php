<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Voucher::query();
        
        if ($request->has('kode')) {
            $query->where('kode', 'like', '%' . $request->kode . '%');
        }
        
        if ($request->has('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $vouchers = $query->orderBy('created_at', 'desc')->get();
        return response()->json($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoucherRequest $request): JsonResponse
    {
        $voucher = Voucher::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'nilai' => $request->nilai,
            'minimal_pembelian' => $request->minimal_pembelian,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'kuota' => $request->kuota,
            'status' => $request->status,
            'is_active' => true,
        ]);

        return response()->json($voucher, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $voucher = Voucher::findOrFail($id);
        return response()->json($voucher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VoucherRequest $request, string $id): JsonResponse
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update($request->validated());
        
        return response()->json($voucher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return response()->json(null, 204);
    }

    /**
     * Validate voucher code
     */
    public function validateVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'kode' => 'required|string',
            'total_pembelian' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('kode', $request->kode)
                          ->where('is_active', true)
                          ->where('tanggal_mulai', '<=', now())
                          ->where('tanggal_berakhir', '>=', now())
                          ->where('penggunaan_aktual', '<', 'max_penggunaan')
                          ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Voucher tidak valid'], 400);
        }

        if ($request->total_pembelian < $voucher->min_pembelian) {
            return response()->json([
                'message' => 'Minimal pembelian tidak terpenuhi',
                'min_pembelian' => $voucher->min_pembelian
            ], 400);
        }

        $diskon = $voucher->jenis === 'percentage' 
            ? ($request->total_pembelian * $voucher->nilai / 100)
            : $voucher->nilai;

        return response()->json([
            'voucher' => $voucher,
            'diskon' => $diskon,
            'total_setelah_diskon' => $request->total_pembelian - $diskon
        ]);
    }
}
