<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:72'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.max'      => 'Password maksimal 72 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => 'email',
            'password' => 'password',
        ];
    }
}
