<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenjualanBarangRequest;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Staff;
use App\Models\KartuStok;
use App\Models\Barang;
use App\Utils\Generator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Penjualan::with(['pasien', 'staff', 'details.barang']);
        
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('pasien_id')) {
            $query->where('pasien_id', $request->pasien_id);
        }
        
        // Filter berdasarkan lokasi barang (apotek/gudang)
        if ($request->has('lokasi_barang')) {
            $lokasi = $request->lokasi_barang;
            $query->whereHas('details.barang', function($q) use ($lokasi) {
                $q->where('lokasi_barang', $lokasi);
            });
        }
        
        $penjualans = $query->orderBy('tanggal', 'desc')->get();
        return response()->json($penjualans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PenjualanBarangRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Get default staff_id if not provided
            $staffId = $request->staff_id;
            if (!$staffId) {
                $defaultStaff = Staff::where('is_active', true)->first();
                if (!$defaultStaff) {
                    throw new \Exception('No active staff found. Please provide staff_id or create an active staff.');
                }
                $staffId = $defaultStaff->id;
            }

            // Generate nomor kwitansi (no_invoice) if not provided
            $noInvoice = $request->no_invoice;
            if (!$noInvoice) {
                $noInvoice = Generator::generateID('PJL');
                // Ensure uniqueness
                while (Penjualan::where('no_invoice', $noInvoice)->exists()) {
                    $noInvoice = Generator::generateID('PJL');
                }
            }

            $penjualan = Penjualan::create([
                'kode' => Generator::generateID('PJL'),
                'pasien_id' => $request->pasien_id ?? null,
                'staff_id' => $staffId,
                'tanggal' => $request->tanggal,
                'no_invoice' => $noInvoice,
                'status' => $request->status ?? 'draft',
                'keterangan' => $request->keterangan,
                'total_harga' => 0,
                'is_active' => true,
            ]);

            $totalHarga = 0;
            foreach ($request->details as $detail) {
                // Use subtotal from request if provided, otherwise calculate
                $subtotal = $detail['subtotal'] ?? ($detail['qty'] * $detail['harga_jual']);
                $totalHarga += $subtotal;
                
                $penjualanDetail = PenjualanDetail::create([
                    'kode' => Generator::generateID('PJD'),
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $detail['barang_id'],
                    'qty' => $detail['qty'],
                    'harga_jual' => $detail['harga_jual'],
                    'subtotal' => $subtotal,
                    'is_active' => true,
                ]);

                // Create kartu stok entry
                $barang = Barang::find($detail['barang_id']);
                if ($barang) {
                    // Get last saldo for this barang
                    $lastKartuStok = KartuStok::where('barang_id', $detail['barang_id'])
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

                    $saldoAwal = $lastKartuStok ? $lastKartuStok->saldo : ($barang->stok_aktual ?? 0);
                    $saldo = $saldoAwal - $detail['qty']; // Decrease stock for sales

                    $kartuStok = KartuStok::create([
                        'kode' => Generator::generateID('KST'),
                        'barang_id' => $detail['barang_id'],
                        'tanggal' => $request->tanggal,
                        'keterangan' => 'Penjualan - No. Invoice: ' . $request->no_invoice,
                        'qty_masuk' => 0,
                        'qty_keluar' => $detail['qty'],
                        'saldo' => $saldo,
                        'referensi' => $penjualan->kode,
                        'is_active' => true,
                    ]);

                    // Update stok_aktual
                    $barang->stok_aktual = $saldo;
                    $barang->save();
                }
            }

            $penjualan->update(['total_harga' => $totalHarga]);
            
            DB::commit();
            
            $penjualan->load(['pasien', 'staff', 'details.barang']);
            return response()->json($penjualan, 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            $errorMessage = 'Failed to create sale';
            if (config('app.debug')) {
                $errorMessage .= ': ' . $e->getMessage();
            }
            return response()->json([
                'message' => $errorMessage,
                'error' => config('app.debug') ? $e->getMessage() : null,
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $penjualan = Penjualan::with(['pasien', 'staff', 'details.barang'])->findOrFail($id);
        return response()->json($penjualan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PenjualanBarangRequest $request, string $id): JsonResponse
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update($request->validated());
        
        return response()->json($penjualan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();
        return response()->json(null, 204);
    }

    /**
     * Update sale status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:draft,confirmed,completed,cancelled',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Sale status updated successfully',
            'penjualan' => $penjualan
        ]);
    }
}
