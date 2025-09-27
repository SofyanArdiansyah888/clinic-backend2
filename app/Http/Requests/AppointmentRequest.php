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
            'pasien_id' => 'required|exists:pasien,id',
            'staff_id' => 'required|exists:staff,id',
            'tanggal' => 'required|date|after:today',
            'waktu' => 'required|date_format:H:i',
            'jenis' => 'required|in:umum,spesialis,emergency',
            'catatan' => 'nullable|string',
            'status' => 'required|in:terjadwal,selesai,dibatalkan',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['pasien_id'] = 'sometimes|exists:pasien,id';
            $rules['staff_id'] = 'sometimes|exists:staff,id';
            $rules['tanggal'] = 'sometimes|date';
            $rules['waktu'] = 'sometimes|date_format:H:i';
            $rules['jenis'] = 'sometimes|in:umum,spesialis,emergency';
            $rules['status'] = 'sometimes|in:terjadwal,selesai,dibatalkan';
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
            'tanggal.after' => 'Tanggal harus setelah hari ini',
            'waktu.required' => 'Waktu wajib diisi',
            'waktu.date_format' => 'Format waktu tidak valid (HH:MM)',
            'jenis.required' => 'Jenis appointment wajib diisi',
            'jenis.in' => 'Jenis appointment tidak valid',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ];
    }
}
