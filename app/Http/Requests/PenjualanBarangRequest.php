<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenjualanBarangRequest extends FormRequest
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
            'no_invoice' => 'nullable|string|unique:penjualans,no_invoice',
            'status' => 'required|in:draft,confirmed,completed,cancelled',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga_jual' => 'required|numeric|min:0',
            'details.*.diskon' => 'nullable|numeric|min:0',
            'details.*.subtotal' => 'nullable|numeric|min:0',
            'pasien_id' => 'nullable|exists:pasiens,id',
            'staff_id' => 'nullable|exists:staffs,id',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['tanggal'] = 'sometimes|date';
            $rules['no_invoice'] = 'sometimes|string|unique:penjualans,no_invoice,' . $this->route('id');
            $rules['status'] = 'sometimes|in:draft,confirmed,completed,cancelled';
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
            'no_invoice.required' => 'No. Invoice wajib diisi',
            'no_invoice.unique' => 'No. Invoice sudah digunakan',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
            'details.required' => 'Detail penjualan wajib diisi',
            'details.min' => 'Minimal 1 item harus ditambahkan',
            'details.*.barang_id.required' => 'Barang wajib dipilih',
            'details.*.barang_id.exists' => 'Barang tidak ditemukan',
            'details.*.qty.required' => 'Jumlah wajib diisi',
            'details.*.qty.integer' => 'Jumlah harus berupa angka',
            'details.*.qty.min' => 'Jumlah minimal 1',
            'details.*.harga_jual.required' => 'Harga jual wajib diisi',
            'details.*.harga_jual.numeric' => 'Harga jual harus berupa angka',
            'details.*.harga_jual.min' => 'Harga jual minimal 0',
            'pasien_id.exists' => 'Pasien tidak ditemukan',
            'staff_id.exists' => 'Staff tidak ditemukan',
        ];
    }
}
