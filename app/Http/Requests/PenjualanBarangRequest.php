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
            'details.*.jenis_penjualan' => 'required|in:barang,treatment',
            'details.*.barang_id' => 'required_if:details.*.jenis_penjualan,barang|nullable|exists:barangs,id',
            'details.*.treatment_id' => 'required_if:details.*.jenis_penjualan,treatment|nullable|exists:treatments,id',
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
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('details')) {
                foreach ($this->input('details', []) as $index => $detail) {
                    $jenisPenjualan = $detail['jenis_penjualan'] ?? null;
                    
                    if ($jenisPenjualan === 'barang') {
                        if (empty($detail['barang_id'])) {
                            $validator->errors()->add("details.{$index}.barang_id", 'Barang wajib dipilih untuk jenis penjualan barang');
                        }
                        if (!empty($detail['treatment_id'])) {
                            $validator->errors()->add("details.{$index}.treatment_id", 'Treatment tidak boleh diisi untuk jenis penjualan barang');
                        }
                    } elseif ($jenisPenjualan === 'treatment') {
                        if (empty($detail['treatment_id'])) {
                            $validator->errors()->add("details.{$index}.treatment_id", 'Treatment wajib dipilih untuk jenis penjualan treatment');
                        }
                        if (!empty($detail['barang_id'])) {
                            $validator->errors()->add("details.{$index}.barang_id", 'Barang tidak boleh diisi untuk jenis penjualan treatment');
                        }
                    }
                }
            }
        });
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
            'details.*.jenis_penjualan.required' => 'Jenis penjualan wajib diisi',
            'details.*.jenis_penjualan.in' => 'Jenis penjualan harus barang atau treatment',
            'details.*.barang_id.required_if' => 'Barang wajib dipilih untuk jenis penjualan barang',
            'details.*.barang_id.exists' => 'Barang tidak ditemukan',
            'details.*.treatment_id.required_if' => 'Treatment wajib dipilih untuk jenis penjualan treatment',
            'details.*.treatment_id.exists' => 'Treatment tidak ditemukan',
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
