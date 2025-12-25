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
            'kode' => 'required|string|max:50|unique:staffs,kode',
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:staffs,nip',
            'jabatan' => 'required|string|max:100',
            'departemen' => 'required|string|max:100',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:staffs,email',
            'alamat' => 'nullable|string',
            'tanggal_bergabung' => 'required|date',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional and handle unique validations
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $id = $this->route('id');
            $rules['kode'] = [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('staffs', 'kode')->ignore($id),
            ];
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['nip'] = [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('staffs', 'nip')->ignore($id),
            ];
            $rules['jabatan'] = 'sometimes|string|max:100';
            $rules['departemen'] = 'sometimes|string|max:100';
            $rules['no_telp'] = 'sometimes|nullable|string|max:20';
            $rules['email'] = [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('staffs', 'email')->ignore($id),
            ];
            $rules['alamat'] = 'sometimes|nullable|string';
            $rules['tanggal_bergabung'] = 'sometimes|date';
            $rules['is_active'] = 'sometimes|boolean';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode staff wajib diisi',
            'kode.unique' => 'Kode staff sudah digunakan',
            'nama.required' => 'Nama staff wajib diisi',
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'jabatan.required' => 'Jabatan wajib diisi',
            'departemen.required' => 'Departemen wajib diisi',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'tanggal_bergabung.required' => 'Tanggal bergabung wajib diisi',
            'tanggal_bergabung.date' => 'Format tanggal tidak valid',
        ];
    }
}
