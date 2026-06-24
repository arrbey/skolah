<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProcessScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_code' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_code.required' => 'Kode tiket wajib diisi.',
            'ticket_code.max'      => 'Kode tiket tidak valid.',
        ];
    }
}
