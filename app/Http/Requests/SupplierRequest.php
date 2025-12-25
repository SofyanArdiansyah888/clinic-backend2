<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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
        $supplierId = $this->route('id');
        
        $rules = [
            'kode' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'kode')->ignore($supplierId)],
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];

        // For update operations, make kode optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['kode'] = ['sometimes', 'string', 'max:255', Rule::unique('suppliers', 'kode')->ignore($supplierId)];
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['alamat'] = 'nullable|string';
            $rules['no_telp'] = 'nullable|string|max:20';
            $rules['email'] = 'nullable|email|max:255';
            $rules['contact_person'] = 'nullable|string|max:255';
            $rules['npwp'] = 'nullable|string|max:50';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode supplier wajib diisi',
            'kode.unique' => 'Kode supplier sudah digunakan',
            'kode.max' => 'Kode supplier maksimal 255 karakter',
            'nama.required' => 'Nama supplier wajib diisi',
            'nama.max' => 'Nama supplier maksimal 255 karakter',
            'alamat.string' => 'Alamat harus berupa teks',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'contact_person.max' => 'Contact person maksimal 255 karakter',
            'npwp.max' => 'NPWP maksimal 50 karakter',
        ];
    }
}
