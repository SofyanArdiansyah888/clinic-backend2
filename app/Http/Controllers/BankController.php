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
        $banks = Bank::all();
        return response()->json($banks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BankRequest $request)
    {
        $bank = new Bank([
            'id' => Generator::generateID('BNK'),
            'nama' => $request->nama,
            'kode' => $request->kode,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'is_active' => true,
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

        $bank->is_active = false;
        $bank->save();
        return response()->json(['message' => 'Bank deactivated successfully']);
    }
}
