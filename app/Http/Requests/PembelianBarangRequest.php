<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembelianBarangRequest extends FormRequest
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
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|in:draft,confirmed,received,cancelled',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['supplier_id'] = 'sometimes|exists:suppliers,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['total_harga'] = 'sometimes|numeric|min:0';
            $rules['status'] = 'sometimes|in:draft,confirmed,received,cancelled';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih',
            'supplier_id.exists' => 'Supplier tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'total_harga.required' => 'Total harga wajib diisi',
            'total_harga.numeric' => 'Total harga harus berupa angka',
            'total_harga.min' => 'Total harga minimal 0',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
