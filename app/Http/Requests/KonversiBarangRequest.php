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
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.tipe' => 'required|in:input,output',
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
            'details.required' => 'Detail konversi wajib diisi',
            'details.array' => 'Detail konversi harus berupa array',
            'details.min' => 'Minimal 1 detail konversi',
            'details.*.barang_id.required' => 'Barang wajib dipilih',
            'details.*.barang_id.exists' => 'Barang tidak ditemukan',
            'details.*.qty.required' => 'Jumlah wajib diisi',
            'details.*.qty.integer' => 'Jumlah harus berupa angka bulat',
            'details.*.qty.min' => 'Jumlah minimal 1',
            'details.*.tipe.required' => 'Tipe wajib diisi',
            'details.*.tipe.in' => 'Tipe harus input atau output',
        ];
    }
}
