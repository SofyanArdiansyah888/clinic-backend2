<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembershipRequest extends FormRequest
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
            'jenis' => 'required|in:silver,gold,platinum',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'biaya' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:aktif,nonaktif,expired',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['pasien_id'] = 'sometimes|exists:pasien,id';
            $rules['jenis'] = 'sometimes|in:silver,gold,platinum';
            $rules['tanggal_mulai'] = 'sometimes|date';
            $rules['tanggal_berakhir'] = 'sometimes|date|after:tanggal_mulai';
            $rules['biaya'] = 'sometimes|numeric|min:0';
            $rules['diskon'] = 'sometimes|numeric|min:0|max:100';
            $rules['status'] = 'sometimes|in:aktif,nonaktif,expired';
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
            'jenis.required' => 'Jenis membership wajib diisi',
            'jenis.in' => 'Jenis membership tidak valid',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi',
            'tanggal_berakhir.date' => 'Format tanggal berakhir tidak valid',
            'tanggal_berakhir.after' => 'Tanggal berakhir harus setelah tanggal mulai',
            'biaya.required' => 'Biaya wajib diisi',
            'biaya.numeric' => 'Biaya harus berupa angka',
            'biaya.min' => 'Biaya minimal 0',
            'diskon.required' => 'Diskon wajib diisi',
            'diskon.numeric' => 'Diskon harus berupa angka',
            'diskon.min' => 'Diskon minimal 0',
            'diskon.max' => 'Diskon maksimal 100',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
