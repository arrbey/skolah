<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group'      => ['required', 'string', 'in:general,seo,payment,email,social,landing,maintenance'],
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'group.required' => 'Grup pengaturan wajib diisi.',
            'group.in'       => 'Grup pengaturan tidak valid.',
            'settings.required' => 'Data pengaturan wajib diisi.',
        ];
    }
}
