<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankRequest extends FormRequest
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
            'no_bank' => 'required|string|max:50|unique:banks,no_bank',
            'nama_bank' => 'required|string|max:255',
            'jenis_bank' => 'required|in:bank,e-money',
            'saldo_awal' => 'required|numeric|min:0',
            'no_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];

        // For update operations, make fields optional and handle unique no_bank validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nama_bank'] = 'sometimes|string|max:255';
            $rules['jenis_bank'] = 'sometimes|in:bank,e-money';
            $rules['saldo_awal'] = 'sometimes|numeric|min:0';
            $rules['no_rekening'] = 'sometimes|string|max:50';
            $rules['atas_nama'] = 'sometimes|string|max:255';
            
            // Handle unique no_bank validation for updates
            if ($this->has('no_bank')) {
                $bankId = $this->route('id');
                $rules['no_bank'] = [
                    'sometimes',
                    'string',
                    'max:50',
                    Rule::unique('banks', 'no_bank')->ignore($bankId),
                ];
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
            'no_bank.required' => 'Kode bank wajib diisi',
            'no_bank.unique' => 'Kode bank sudah ada',
            'no_bank.max' => 'Kode bank maksimal 50 karakter',
            'nama_bank.required' => 'Nama bank wajib diisi',
            'nama_bank.max' => 'Nama bank maksimal 255 karakter',
            'jenis_bank.required' => 'Jenis bank wajib dipilih',
            'jenis_bank.in' => 'Jenis bank harus bank atau e-money',
            'saldo_awal.required' => 'Saldo awal wajib diisi',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka',
            'saldo_awal.min' => 'Saldo awal tidak boleh negatif',
            'no_rekening.required' => 'Nomor rekening wajib diisi',
            'no_rekening.max' => 'Nomor rekening maksimal 50 karakter',
            'atas_nama.required' => 'Atas nama wajib diisi',
            'atas_nama.max' => 'Atas nama maksimal 255 karakter',
        ];
    }
}
