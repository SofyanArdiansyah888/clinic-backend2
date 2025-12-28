<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
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
            'pasien_id' => 'required|exists:pasiens,id',
            'staff_id' => 'required|exists:staffs,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i:s',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|in:scheduled,confirmed,in_progress,completed,cancelled',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['pasien_id'] = 'sometimes|exists:pasiens,id';
            $rules['staff_id'] = 'sometimes|exists:staffs,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['jam'] = 'sometimes|date_format:H:i:s';
            $rules['status'] = 'sometimes|in:scheduled,confirmed,in_progress,completed,cancelled';
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
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'jam.required' => 'Jam wajib diisi',
            'jam.date_format' => 'Format jam tidak valid (HH:mm:ss)',
            'status.in' => 'Status tidak valid',
        ];
    }
}
