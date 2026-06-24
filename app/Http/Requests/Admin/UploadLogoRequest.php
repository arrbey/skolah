<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Pilih file logo terlebih dahulu.',
            'logo.image'    => 'File harus berupa gambar.',
            'logo.mimes'    => 'Format logo harus JPG, PNG, SVG, atau WebP.',
            'logo.max'      => 'Ukuran logo maksimal 2 MB.',
        ];
    }
}
