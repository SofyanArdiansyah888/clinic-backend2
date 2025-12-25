<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TreatmentRequest extends FormRequest
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
        $treatmentId = $this->route('id');
        
        $rules = [
            'kode' => ['required', 'string', 'max:255', Rule::unique('treatments', 'kode')->ignore($treatmentId)],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];

        // For update operations, make kode optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['kode'] = ['sometimes', 'string', 'max:255', Rule::unique('treatments', 'kode')->ignore($treatmentId)];
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['deskripsi'] = 'nullable|string';
            $rules['durasi'] = 'sometimes|integer|min:1';
            $rules['harga'] = 'sometimes|numeric|min:0';
            $rules['kategori'] = 'sometimes|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode treatment wajib diisi',
            'kode.unique' => 'Kode treatment sudah digunakan',
            'kode.max' => 'Kode treatment maksimal 255 karakter',
            'nama.required' => 'Nama treatment wajib diisi',
            'nama.max' => 'Nama treatment maksimal 255 karakter',
            'deskripsi.string' => 'Deskripsi harus berupa teks',
            'durasi.required' => 'Durasi wajib diisi',
            'durasi.integer' => 'Durasi harus berupa angka bulat',
            'durasi.min' => 'Durasi minimal 1 menit',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga minimal 0',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.max' => 'Kategori maksimal 255 karakter',
        ];
    }
}
