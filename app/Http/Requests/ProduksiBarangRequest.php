<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProduksiBarangRequest extends FormRequest
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
            'jumlah_produksi' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:planning,in_progress,completed,cancelled',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['barang_id'] = 'sometimes|exists:barangs,id';
            $rules['jumlah_produksi'] = 'sometimes|integer|min:1';
            $rules['tanggal_mulai'] = 'sometimes|date';
            $rules['tanggal_selesai'] = 'sometimes|date|after:tanggal_mulai';
            $rules['status'] = 'sometimes|in:planning,in_progress,completed,cancelled';
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
            'jumlah_produksi.required' => 'Jumlah produksi wajib diisi',
            'jumlah_produksi.integer' => 'Jumlah produksi harus berupa angka bulat',
            'jumlah_produksi.min' => 'Jumlah produksi minimal 1',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
