<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerusahaanRequest extends FormRequest
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
            'alamat' => 'required|string', 
            'telepon' => 'required|string|max:20',
            'website' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/)?([\w\-\.]+\.)+[a-zA-Z]{2,}(\/.*)?$/'],
            'npwp' => 'nullable|string|max:50',
        ];

        // For update operations, make fields optional and handle unique email validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['alamat'] = 'sometimes|string';
            $rules['telepon'] = 'sometimes|string|max:20';
            
            // Handle unique email validation for updates
            // Get the id from route parameter (perusahaan/{id})
            $id = $this->route('id');
            if ($this->has('email')) {
                // For update, ignore the current record's email
                $rules['email'] = [
                    'sometimes',
                    'email',
                    Rule::unique('perusahaans', 'email')->ignore($id, 'id'),
                ];
            } else {
                // Email is optional for update, but if not provided, we don't validate it
                $rules['email'] = 'sometimes|email';
            }
        } else {
            // For store operations, email is required and must be unique
            $rules['email'] = 'required|email|unique:perusahaans,email';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama perusahaan wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'telepon.required' => 'Telepon wajib diisi',
            'telepon.max' => 'Telepon maksimal 20 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'website.regex' => 'Format website tidak valid. Contoh: www.example.com atau https://example.com',
            'website.max' => 'Website maksimal 255 karakter',
            'npwp.max' => 'NPWP maksimal 50 karakter',
        ];
    }

}
