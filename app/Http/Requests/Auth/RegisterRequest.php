<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\-\.]+$/u'],
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
            'terms'    => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.min'           => 'Nama minimal 3 karakter.',
            'name.regex'         => 'Nama hanya boleh mengandung huruf, spasi, titik, dan tanda hubung.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan login.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.max'       => 'Password maksimal 72 karakter.',
            'terms.required'     => 'Anda harus menyetujui syarat dan ketentuan.',
            'terms.accepted'     => 'Anda harus menyetujui syarat dan ketentuan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama lengkap',
            'email'    => 'email',
            'password' => 'password',
            'terms'    => 'syarat dan ketentuan',
        ];
    }
}
