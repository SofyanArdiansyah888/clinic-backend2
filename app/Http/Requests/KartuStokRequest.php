<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KartuStokRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'barang_id' => 'required|exists:barangs,id',
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|in:masuk,keluar,opname,konversi',
            'jumlah' => 'required|integer',
            'stok_awal' => 'required|integer|min:0',
            'stok_akhir' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['barang_id'] = 'sometimes|exists:barangs,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['jenis_transaksi'] = 'sometimes|in:masuk,keluar,opname,konversi';
            $rules['jumlah'] = 'sometimes|integer';
            $rules['stok_awal'] = 'sometimes|integer|min:0';
            $rules['stok_akhir'] = 'sometimes|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'barang_id.required' => 'Barang wajib dipilih',
            'barang_id.exists' => 'Barang tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'jenis_transaksi.required' => 'Jenis transaksi wajib diisi',
            'jenis_transaksi.in' => 'Jenis transaksi tidak valid',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat',
            'stok_awal.required' => 'Stok awal wajib diisi',
            'stok_awal.integer' => 'Stok awal harus berupa angka bulat',
            'stok_awal.min' => 'Stok awal minimal 0',
            'stok_akhir.required' => 'Stok akhir wajib diisi',
            'stok_akhir.integer' => 'Stok akhir harus berupa angka bulat',
            'stok_akhir.min' => 'Stok akhir minimal 0',
        ];
    }
}
