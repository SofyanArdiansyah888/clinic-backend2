<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankRequest extends FormRequest
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
            'kode' => 'required|string|max:10|unique:banks,kode',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional and handle unique kode validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['alamat'] = 'sometimes|string';
            $rules['telepon'] = 'sometimes|string|max:20';
            
            // Handle unique kode validation for updates
            if ($this->has('kode')) {
                $rules['kode'] = [
                    'sometimes',
                    'string',
                    'max:10',
                    Rule::unique('banks', 'kode')->ignore($this->route('bank')),
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
            'nama.required' => 'Nama bank wajib diisi',
            'kode.required' => 'Kode bank wajib diisi',
            'kode.unique' => 'Kode bank sudah ada',
            'kode.max' => 'Kode bank maksimal 10 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'telepon.required' => 'Telepon wajib diisi',
            'telepon.max' => 'Telepon maksimal 20 karakter',
            'email.email' => 'Format email tidak valid',
            'website.url' => 'Format website tidak valid',
        ];
    }
}
