<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\-\.]+$/u'],
            'bio'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min'      => 'Nama minimal 3 karakter.',
            'name.max'      => 'Nama maksimal 100 karakter.',
            'name.regex'    => 'Nama hanya boleh mengandung huruf, spasi, titik, dan tanda hubung.',
            'bio.max'       => 'Bio maksimal 1000 karakter.',
        ];
    }
}
