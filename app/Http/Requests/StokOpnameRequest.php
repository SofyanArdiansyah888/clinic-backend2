<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StokOpnameRequest extends FormRequest
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
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.stok_fisik' => 'required|integer|min:0',
            'details.*.keterangan' => 'nullable|string',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['tanggal'] = 'sometimes|date';
            $rules['details'] = 'sometimes|array|min:1';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'details.required' => 'Detail stok opname wajib diisi',
            'details.min' => 'Minimal 1 item harus ditambahkan',
            'details.*.barang_id.required' => 'Barang wajib dipilih',
            'details.*.barang_id.exists' => 'Barang tidak ditemukan',
            'details.*.stok_fisik.required' => 'Stok fisik wajib diisi',
            'details.*.stok_fisik.integer' => 'Stok fisik harus berupa angka',
            'details.*.stok_fisik.min' => 'Stok fisik minimal 0',
        ];
    }
}
