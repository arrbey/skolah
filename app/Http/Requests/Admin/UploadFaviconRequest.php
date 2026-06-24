<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadFaviconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'favicon' => ['required', 'image', 'mimes:jpg,jpeg,png,ico,webp', 'max:512'],
        ];
    }

    public function messages(): array
    {
        return [
            'favicon.required' => 'Pilih file favicon terlebih dahulu.',
            'favicon.image'    => 'File harus berupa gambar.',
            'favicon.mimes'    => 'Format favicon harus JPG, PNG, ICO, atau WebP.',
            'favicon.max'      => 'Ukuran favicon maksimal 512 KB.',
        ];
    }
}
