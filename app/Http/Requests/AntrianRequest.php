<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AntrianRequest extends FormRequest
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
            'pasien_id' => 'required|exists:pasien,id',
            'cabang_id' => 'required|exists:cabang,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:menunggu,dalam_proses,selesai,dibatalkan',
            'catatan' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['pasien_id'] = 'sometimes|exists:pasien,id';
            $rules['cabang_id'] = 'sometimes|exists:cabang,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['status'] = 'sometimes|in:menunggu,dalam_proses,selesai,dibatalkan';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pasien_id.required' => 'Pasien wajib dipilih',
            'pasien_id.exists' => 'Pasien tidak ditemukan',
            'cabang_id.required' => 'Cabang wajib dipilih',
            'cabang_id.exists' => 'Cabang tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
