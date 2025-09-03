<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StokOpnameRequest extends FormRequest
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
            'tanggal' => 'required|date',
            'cabang_id' => 'required|exists:cabang,id',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:draft,selesai,dibatalkan',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['tanggal'] = 'sometimes|date';
            $rules['cabang_id'] = 'sometimes|exists:cabang,id';
            $rules['status'] = 'sometimes|in:draft,selesai,dibatalkan';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'cabang_id.required' => 'Cabang wajib dipilih',
            'cabang_id.exists' => 'Cabang tidak ditemukan',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
