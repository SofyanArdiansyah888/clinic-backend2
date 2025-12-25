<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,staff,doctor',
            'is_active' => 'boolean',
            'hak_akses' => 'nullable|array',
        ];

        // For update operations, make fields optional and handle unique validations
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $id = $this->route('id');
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['username'] = [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($id),
            ];
            $rules['email'] = [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ];
            $rules['password'] = 'sometimes|string|min:6';
            $rules['role'] = 'sometimes|string|in:admin,staff,doctor';
            $rules['hak_akses'] = 'sometimes|nullable|array';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'username.max' => 'Username maksimal 50 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.required' => 'Role wajib diisi',
            'role.in' => 'Role tidak valid',
        ];
    }
}
