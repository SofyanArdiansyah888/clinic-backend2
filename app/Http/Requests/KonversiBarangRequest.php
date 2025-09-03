<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KonversiBarangRequest extends FormRequest
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
            'barang_asal_id' => 'required|exists:barangs,id',
            'barang_tujuan_id' => 'required|exists:barangs,id',
            'jumlah_asal' => 'required|integer|min:1',
            'jumlah_tujuan' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['barang_asal_id'] = 'sometimes|exists:barangs,id';
            $rules['barang_tujuan_id'] = 'sometimes|exists:barangs,id';
            $rules['jumlah_asal'] = 'sometimes|integer|min:1';
            $rules['jumlah_tujuan'] = 'sometimes|integer|min:1';
            $rules['tanggal'] = 'sometimes|date';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'barang_asal_id.required' => 'Barang asal wajib dipilih',
            'barang_asal_id.exists' => 'Barang asal tidak ditemukan',
            'barang_tujuan_id.required' => 'Barang tujuan wajib dipilih',
            'barang_tujuan_id.exists' => 'Barang tujuan tidak ditemukan',
            'jumlah_asal.required' => 'Jumlah asal wajib diisi',
            'jumlah_asal.integer' => 'Jumlah asal harus berupa angka bulat',
            'jumlah_asal.min' => 'Jumlah asal minimal 1',
            'jumlah_tujuan.required' => 'Jumlah tujuan wajib diisi',
            'jumlah_tujuan.integer' => 'Jumlah tujuan harus berupa angka bulat',
            'jumlah_tujuan.min' => 'Jumlah tujuan minimal 1',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
        ];
    }
}
