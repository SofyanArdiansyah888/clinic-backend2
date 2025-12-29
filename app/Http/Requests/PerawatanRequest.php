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
        // Check if request has details array (new format for treatment reguler)
        if ($this->has('details')) {
            $rules = [
                'pasien_id' => 'required|exists:pasiens,id',
                'staff_id' => 'required|exists:staffs,id',
                'tanggal' => 'required|date',
                'diagnosa' => 'required|string',
                'details' => 'required|array|min:1',
                'details.*.treatment_id' => 'required|exists:treatments,id',
                'details.*.tindakan' => 'required|string',
                'details.*.jumlah' => 'nullable|integer|min:1',
                'details.*.beautician_id' => 'nullable|exists:staffs,id',
                'details.*.harga' => 'required|numeric|min:0',
                'details.*.diskon' => 'nullable|numeric|min:0',
                'details.*.rp_percent' => 'nullable|numeric|min:0',
                'details.*.total' => 'required|numeric|min:0',
                'details.*.catatan' => 'nullable|string',
            ];
        } else {
            // Old format (single perawatan) or new detail perawatan format
            $rules = [
                'pasien_id' => 'required|exists:pasiens,id',
                'staff_id' => 'required|exists:staffs,id',
                'antrian_id' => 'nullable|exists:antrians,id',
                'treatment_id' => 'sometimes|exists:treatments,id',
                'tanggal' => 'required|date',
                'diagnosa' => 'required|string',
                'tindakan' => 'sometimes|string',
                'resep' => 'nullable|string',
                'catatan' => 'nullable|string',
                'biaya' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:planned,in_progress,completed,cancelled',
                'is_active' => 'boolean',
                // New fields
                'anamnesis' => 'nullable|string',
                'pemeriksaan_awal' => 'nullable|string',
                'pemeriksaan' => 'nullable|string',
                'kunjungan_berikutnya' => 'nullable|date',
                'foto_perawatan' => 'nullable|array',
                'foto_sebelum' => 'nullable|array',
                'foto_sesudah' => 'nullable|array',
                // Nested data
                'reseps' => 'nullable|array',
                'reseps.*.antrian_id' => 'required|exists:antrians,id',
                'reseps.*.tanggal' => 'nullable|date',
                'reseps.*.status' => 'nullable|in:draft,confirmed,completed,cancelled',
                'reseps.*.barangs' => 'required|array|min:1',
                'reseps.*.barangs.*.barang_id' => 'required|exists:barangs,id',
                'reseps.*.barangs.*.kode_barang' => 'required|string',
                'reseps.*.barangs.*.nama_barang' => 'required|string',
                'reseps.*.barangs.*.jumlah' => 'required|numeric|min:0',
                'reseps.*.barangs.*.unit' => 'required|string',
                'reseps.*.barangs.*.harga' => 'nullable|numeric|min:0',
                'reseps.*.barangs.*.total' => 'required|numeric|min:0',
                'tindakans' => 'nullable|array',
                'tindakans.*.treatment_id' => 'required|exists:treatments,id',
                'tindakans.*.tanggal' => 'nullable|date',
                'tindakans.*.jumlah' => 'required|integer|min:1',
                'tindakans.*.beautician_id' => 'nullable|exists:staffs,id',
                'tindakans.*.harga' => 'required|numeric|min:0',
                'tindakans.*.diskon' => 'nullable|numeric|min:0',
                'tindakans.*.rp_percent' => 'nullable|numeric|min:0',
                'tindakans.*.total' => 'required|numeric|min:0',
                'tindakans.*.status' => 'nullable|in:draft,confirmed,completed,cancelled',
                'tindakans.*.catatan' => 'nullable|string',
            ];
        }

        // For update operations, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            if (isset($rules['pasien_id'])) {
                $rules['pasien_id'] = 'sometimes|exists:pasiens,id';
            }
            if (isset($rules['staff_id'])) {
                $rules['staff_id'] = 'sometimes|exists:staffs,id';
            }
            if (isset($rules['treatment_id'])) {
                $rules['treatment_id'] = 'sometimes|exists:treatments,id';
            }
            if (isset($rules['tanggal'])) {
                $rules['tanggal'] = 'sometimes|date';
            }
            if (isset($rules['diagnosa'])) {
                $rules['diagnosa'] = 'sometimes|string';
            }
            if (isset($rules['tindakan'])) {
                $rules['tindakan'] = 'sometimes|string';
            }
            if (isset($rules['biaya'])) {
                $rules['biaya'] = 'sometimes|numeric|min:0';
            }
            if (isset($rules['status'])) {
                $rules['status'] = 'sometimes|in:belum_selesai,selesai,dibatalkan';
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
            'details.required' => 'Detail treatment wajib diisi',
            'details.array' => 'Detail treatment harus berupa array',
            'details.min' => 'Minimal 1 treatment harus ditambahkan',
            'details.*.treatment_id.required' => 'Treatment wajib dipilih',
            'details.*.treatment_id.exists' => 'Treatment tidak ditemukan',
            'details.*.tindakan.required' => 'Tindakan wajib diisi',
            'details.*.jumlah.integer' => 'Jumlah harus berupa angka',
            'details.*.jumlah.min' => 'Jumlah minimal 1',
            'details.*.beautician_id.exists' => 'Beautician tidak ditemukan',
            'details.*.harga.required' => 'Harga wajib diisi',
            'details.*.harga.numeric' => 'Harga harus berupa angka',
            'details.*.harga.min' => 'Harga minimal 0',
            'details.*.diskon.numeric' => 'Diskon harus berupa angka',
            'details.*.diskon.min' => 'Diskon minimal 0',
            'details.*.total.required' => 'Total wajib diisi',
            'details.*.total.numeric' => 'Total harus berupa angka',
            'details.*.total.min' => 'Total minimal 0',
        ];
    }
}
