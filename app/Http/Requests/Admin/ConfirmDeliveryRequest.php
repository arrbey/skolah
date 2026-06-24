<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'note'           => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_photo.required' => 'Foto bukti pengiriman wajib diunggah.',
            'delivery_photo.image'    => 'File harus berupa gambar.',
            'delivery_photo.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'delivery_photo.max'      => 'Ukuran foto maksimal 3 MB.',
            'note.max'                => 'Catatan maksimal 500 karakter.',
        ];
    }
}
