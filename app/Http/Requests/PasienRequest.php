<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasienRequest extends FormRequest
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
        $pasienId = $this->route('id');
        
        $rules = [
            'kode' => ['required', 'string', 'max:255', Rule::unique('pasiens', 'kode')->ignore($pasienId)],
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'is_active' => 'boolean',
        ];

        // For update operations, make kode optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['kode'] = ['sometimes', 'string', 'max:255', Rule::unique('pasiens', 'kode')->ignore($pasienId)];
            $rules['nama'] = 'sometimes|string|max:255';
            $rules['alamat'] = 'nullable|string';
            $rules['no_telp'] = 'nullable|string|max:20';
            $rules['email'] = 'nullable|email|max:255';
            $rules['tanggal_lahir'] = 'nullable|date';
            $rules['jenis_kelamin'] = 'nullable|in:L,P';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode pasien wajib diisi',
            'kode.unique' => 'Kode pasien sudah digunakan',
            'kode.max' => 'Kode pasien maksimal 255 karakter',
            'nama.required' => 'Nama pasien wajib diisi',
            'nama.max' => 'Nama pasien maksimal 255 karakter',
            'alamat.string' => 'Alamat harus berupa teks',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid (harus L atau P)',
        ];
    }
}
