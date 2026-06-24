<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_notes' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_notes.required' => 'Berikan alasan penolakan.',
            'admin_notes.min'      => 'Alasan penolakan minimal 10 karakter.',
            'admin_notes.max'      => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
