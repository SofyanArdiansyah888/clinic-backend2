<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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
            'email' => 'required|email|unique:staff,email',
            'jabatan' => 'required|string|max:100',
            'tanggal_bergabung' => 'required|date',
            'gaji' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional and handle unique email validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['alamat'] = 'sometimes|string';
            $rules['telepon'] = 'sometimes|string|max:20';
            $rules['jabatan'] = 'sometimes|string|max:100';
            $rules['tanggal_bergabung'] = 'sometimes|date';
            $rules['gaji'] = 'sometimes|numeric|min:0';
            
            // Handle unique email validation for updates
            if ($this->has('email')) {
                $rules['email'] = [
                    'sometimes',
                    'email',
                    Rule::unique('staff', 'email')->ignore($this->route('staff')),
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
            'nama.required' => 'Nama staff wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'telepon.required' => 'Telepon wajib diisi',
            'telepon.max' => 'Telepon maksimal 20 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'jabatan.required' => 'Jabatan wajib diisi',
            'tanggal_bergabung.required' => 'Tanggal bergabung wajib diisi',
            'tanggal_bergabung.date' => 'Format tanggal tidak valid',
            'gaji.required' => 'Gaji wajib diisi',
            'gaji.numeric' => 'Gaji harus berupa angka',
            'gaji.min' => 'Gaji minimal 0',
        ];
    }
}
