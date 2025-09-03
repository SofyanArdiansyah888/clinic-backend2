<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerawatanRequest extends FormRequest
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
            'staff_id' => 'required|exists:staff,id',
            'treatment_id' => 'required|exists:treatments,id',
            'tanggal' => 'required|date',
            'diagnosa' => 'required|string',
            'tindakan' => 'required|string',
            'resep' => 'nullable|string',
            'catatan' => 'nullable|string',
            'biaya' => 'required|numeric|min:0',
            'status' => 'required|in:belum_selesai,selesai,dibatalkan',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['pasien_id'] = 'sometimes|exists:pasien,id';
            $rules['staff_id'] = 'sometimes|exists:staff,id';
            $rules['treatment_id'] = 'sometimes|exists:treatments,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['diagnosa'] = 'sometimes|string';
            $rules['tindakan'] = 'sometimes|string';
            $rules['biaya'] = 'sometimes|numeric|min:0';
            $rules['status'] = 'sometimes|in:belum_selesai,selesai,dibatalkan';
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
            'staff_id.required' => 'Staff wajib dipilih',
            'staff_id.exists' => 'Staff tidak ditemukan',
            'treatment_id.required' => 'Treatment wajib dipilih',
            'treatment_id.exists' => 'Treatment tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'diagnosa.required' => 'Diagnosa wajib diisi',
            'tindakan.required' => 'Tindakan wajib diisi',
            'biaya.required' => 'Biaya wajib diisi',
            'biaya.numeric' => 'Biaya harus berupa angka',
            'biaya.min' => 'Biaya minimal 0',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
