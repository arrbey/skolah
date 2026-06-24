<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'min:3', 'max:255'],
            'author'           => ['required', 'string', 'max:255'],
            'instructor_id'    => ['required', 'integer', 'exists:users,id'],
            'institution_id'   => ['nullable', 'integer', 'exists:institutions,id'],
            'description'      => ['nullable', 'string', 'max:50000'],
            'price'            => ['required', 'integer', 'min:0', 'max:99999999'],
            'discount_price'   => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'type'             => ['required', 'in:physical,digital,both'],
            'stock'            => ['nullable', 'integer', 'min:0', 'max:99999'],
            'isbn'             => ['nullable', 'string', 'max:30', 'regex:/^[\d\-X]+$/i'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'pages'            => ['nullable', 'integer', 'min:1', 'max:99999'],
            'status'           => ['required', 'in:draft,published'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'file_path'        => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul buku wajib diisi.',
            'author.required'      => 'Nama penulis wajib diisi.',
            'price.required'       => 'Harga wajib diisi.',
            'type.required'        => 'Tipe buku wajib dipilih.',
            'cover_image.image'    => 'Cover harus berupa gambar.',
            'file_path.mimes'      => 'File buku harus berformat PDF.',
            'file_path.max'        => 'Ukuran file maksimal 50 MB.',
            'status.required'      => 'Status wajib dipilih.',
        ];
    }
}
