<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'     => ['required', 'string', 'max:255'],
            'image'     => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'link'      => ['nullable', 'url', 'max:500'],
            'position'  => ['nullable', 'string', 'in:hero,promo,sidebar'],
            'order'     => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul banner wajib diisi.',
            'image.required' => 'Gambar banner wajib diunggah.',
            'image.image'    => 'File harus berupa gambar.',
            'image.max'      => 'Ukuran gambar maksimal 10 MB.',
        ];
    }
}
