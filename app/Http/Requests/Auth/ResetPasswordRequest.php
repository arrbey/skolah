<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            // JANGAN gunakan 'exists:users,email' — info disclosure vulnerability
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'Token reset password tidak valid.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.max'       => 'Password maksimal 72 karakter.',
        ];
    }
}
