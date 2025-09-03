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
            'pasien_id' => 'required|exists:pasien,id',
            'tanggal' => 'required|date',
            'total_harga' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'total_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,debit,credit',
            'status' => 'required|in:draft,confirmed,completed,cancelled',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['pasien_id'] = 'sometimes|exists:pasien,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['total_harga'] = 'sometimes|numeric|min:0';
            $rules['diskon'] = 'sometimes|numeric|min:0';
            $rules['total_bayar'] = 'sometimes|numeric|min:0';
            $rules['metode_pembayaran'] = 'sometimes|in:cash,debit,credit';
            $rules['status'] = 'sometimes|in:draft,confirmed,completed,cancelled';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pasien_id.required' => 'Pasien wajib dipilih',
            'pasien_id.exists' => 'Pasien tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'total_harga.required' => 'Total harga wajib diisi',
            'total_harga.numeric' => 'Total harga harus berupa angka',
            'total_harga.min' => 'Total harga minimal 0',
            'diskon.numeric' => 'Diskon harus berupa angka',
            'diskon.min' => 'Diskon minimal 0',
            'total_bayar.required' => 'Total bayar wajib diisi',
            'total_bayar.numeric' => 'Total bayar harus berupa angka',
            'total_bayar.min' => 'Total bayar minimal 0',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
