<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
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
            'kode' => 'required|string|max:50|unique:vouchers,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:diskon,potongan_harga,buy_one_get_one',
            'nilai' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'minimal_pembelian' => 'nullable|numeric|min:0',
            'maksimal_diskon' => 'nullable|numeric|min:0',
            'kuota' => 'nullable|integer|min:1',
            'status' => 'required|in:aktif,nonaktif,expired',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional and handle unique kode validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['jenis'] = 'sometimes|in:diskon,potongan_harga,buy_one_get_one';
            $rules['nilai'] = 'sometimes|numeric|min:0';
            $rules['tanggal_mulai'] = 'sometimes|date';
            $rules['tanggal_berakhir'] = 'sometimes|date|after:tanggal_mulai';
            $rules['minimal_pembelian'] = 'sometimes|numeric|min:0';
            $rules['maksimal_diskon'] = 'sometimes|numeric|min:0';
            $rules['kuota'] = 'sometimes|integer|min:1';
            $rules['status'] = 'sometimes|in:aktif,nonaktif,expired';
            
            // Handle unique kode validation for updates
            if ($this->has('kode')) {
                $rules['kode'] = [
                    'sometimes',
                    'string',
                    'max:50',
                    Rule::unique('vouchers', 'kode')->ignore($this->route('voucher')),
                ];
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode voucher wajib diisi',
            'kode.unique' => 'Kode voucher sudah ada',
            'kode.max' => 'Kode voucher maksimal 50 karakter',
            'nama.required' => 'Nama voucher wajib diisi',
            'jenis.required' => 'Jenis voucher wajib diisi',
            'jenis.in' => 'Jenis voucher tidak valid',
            'nilai.required' => 'Nilai voucher wajib diisi',
            'nilai.numeric' => 'Nilai voucher harus berupa angka',
            'nilai.min' => 'Nilai voucher minimal 0',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi',
            'tanggal_berakhir.date' => 'Format tanggal berakhir tidak valid',
            'tanggal_berakhir.after' => 'Tanggal berakhir harus setelah tanggal mulai',
            'minimal_pembelian.numeric' => 'Minimal pembelian harus berupa angka',
            'minimal_pembelian.min' => 'Minimal pembelian minimal 0',
            'maksimal_diskon.numeric' => 'Maksimal diskon harus berupa angka',
            'maksimal_diskon.min' => 'Maksimal diskon minimal 0',
            'kuota.integer' => 'Kuota harus berupa angka bulat',
            'kuota.min' => 'Kuota minimal 1',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
