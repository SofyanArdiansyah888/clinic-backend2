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
            'staff_id' => 'nullable|exists:staffs,id',
            'tanggal' => 'required|date',
            'no_invoice' => 'required|string|unique:pembelians,no_invoice',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|in:draft,ordered,received,cancelled',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['supplier_id'] = 'sometimes|exists:suppliers,id';
            $rules['staff_id'] = 'sometimes|exists:staffs,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['no_invoice'] = 'sometimes|string|unique:pembelians,no_invoice,' . $this->route('id');
            $rules['total_harga'] = 'sometimes|numeric|min:0';
            $rules['status'] = 'sometimes|in:draft,ordered,received,cancelled';
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
