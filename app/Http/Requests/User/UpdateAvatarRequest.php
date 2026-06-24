<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Pilih file foto terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Format gambar harus JPG, PNG, atau WebP.',
            'avatar.max'      => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
