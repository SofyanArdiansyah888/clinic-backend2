<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Utils\Generator;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Bank::query();
        
        // Search by nama_bank
        if (request()->has('search') && request('search')) {
            $query->where('nama_bank', 'like', '%' . request('search') . '%');
        }
        
        // Filter by jenis_bank
        if (request()->has('jenis_bank') && request('jenis_bank')) {
            $query->where('jenis_bank', request('jenis_bank'));
        }
        
        // Filter by is_active
        if (request()->has('is_active')) {
            $query->where('is_active', request('is_active'));
        }
        
        // Pagination
        $perPage = request('per_page', 10);
        $page = request('page', 1);
        
        $banks = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        
        // Format response sesuai dengan ResponseListType di frontend
        return response()->json([
            'data' => $banks->items(),
            'page' => $banks->currentPage(),
            'page_size' => $banks->perPage(),
            'total_pages' => $banks->total(),
            'total_rows' => $banks->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BankRequest $request)
    {
        $bank = new Bank([
            'id' => Generator::generateID('BNK'),
            'no_bank' => $request->no_bank,
            'nama_bank' => $request->nama_bank,
            'jenis_bank' => $request->jenis_bank,
            'saldo_awal' => $request->saldo_awal,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'is_active' => $request->is_active ?? true,
        ]);
        $bank->save();

        return response()->json($bank, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return response()->json(['message' => 'Bank not found'], 404);
        }
        return response()->json($bank);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BankRequest $request, string $id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return response()->json(['message' => 'Bank not found'], 404);
        }

        $bank->update($request->validated());
        return response()->json($bank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return response()->json(['message' => 'Bank not found'], 404);
        }

        $bank->delete();
        return response()->json(null, 204);
    }
}
