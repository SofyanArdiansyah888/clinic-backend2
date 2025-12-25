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
        $barangId = $this->route('id');
        
        $rules = [
            'kode' => ['required', 'string', 'max:255', Rule::unique('barangs', 'kode')->ignore($barangId)],
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'stok_aktual' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['kode'] = ['sometimes', 'string', 'max:255', Rule::unique('barangs', 'kode')->ignore($barangId)];
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['kategori'] = 'sometimes|string|max:100';
            $rules['satuan'] = 'sometimes|string|max:50';
            $rules['harga_beli'] = 'sometimes|numeric|min:0';
            $rules['harga_jual'] = 'sometimes|numeric|min:0';
            $rules['stok_minimal'] = 'sometimes|integer|min:0';
            $rules['stok_aktual'] = 'sometimes|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode barang wajib diisi',
            'kode.unique' => 'Kode barang sudah digunakan',
            'kode.max' => 'Kode barang maksimal 255 karakter',
            'nama.required' => 'Nama barang wajib diisi',
            'nama.max' => 'Nama barang maksimal 255 karakter',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.max' => 'Kategori maksimal 100 karakter',
            'satuan.required' => 'Satuan wajib diisi',
            'satuan.max' => 'Satuan maksimal 50 karakter',
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
