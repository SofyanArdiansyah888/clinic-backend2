<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarangRequest extends FormRequest
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
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'stok_aktual' => 'required|integer|min:0',
        ];

        // For update operations, make fields optional and handle unique kode validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['kategori'] = 'sometimes|string|max:100';
            $rules['satuan'] = 'sometimes|string|max:50';
            $rules['harga_beli'] = 'sometimes|numeric|min:0';
            $rules['harga_jual'] = 'sometimes|numeric|min:0';
            $rules['stok_minimal'] = 'sometimes|integer|min:0';
            $rules['stok_aktual'] = 'sometimes|integer|min:0';
            
            // Handle unique kode validation for updates
            if ($this->has('kode')) {
                $rules['kode'] = [
                    'sometimes',
                    'string',
                    Rule::unique('barangs', 'kode')->ignore($this->route('barang')),
                ];
            }
        } else {
            // For store operations, kode is required and must be unique
            $rules['kode'] = 'required|string|unique:barangs,kode';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama barang wajib diisi',
            'kode.required' => 'Kode barang wajib diisi',
            'kode.unique' => 'Kode barang sudah ada',
            'kategori.required' => 'Kategori wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
            'harga_beli.required' => 'Harga beli wajib diisi',
            'harga_beli.numeric' => 'Harga beli harus berupa angka',
            'harga_beli.min' => 'Harga beli minimal 0',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'harga_jual.numeric' => 'Harga jual harus berupa angka',
            'harga_jual.min' => 'Harga jual minimal 0',
            'stok_minimal.required' => 'Stok minimal wajib diisi',
            'stok_minimal.integer' => 'Stok minimal harus berupa angka bulat',
            'stok_minimal.min' => 'Stok minimal minimal 0',
            'stok_aktual.required' => 'Stok aktual wajib diisi',
            'stok_aktual.integer' => 'Stok aktual harus berupa angka bulat',
            'stok_aktual.min' => 'Stok aktual minimal 0',
        ];
    }
}
