<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'min:3', 'max:255'],
            'description'      => ['required', 'string', 'min:20', 'max:50000'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price'            => ['required', 'integer', 'min:0', 'max:99999999'],
            'discount_price'   => ['nullable', 'integer', 'min:0', 'max:99999999', 'lt:price'],
            'type'             => ['required', 'in:physical,digital,both'],
            'stock'            => ['nullable', 'integer', 'min:0', 'max:99999'],
            'institution_id'   => ['nullable', 'exists:institutions,id'],
            'file_path'        => ['nullable', 'mimes:pdf', 'max:51200'],
            'isbn'             => ['nullable', 'string', 'max:30', 'regex:/^[\d\-X]+$/i'],
            'author'           => ['required', 'string', 'max:255'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'pages'            => ['nullable', 'integer', 'min:1', 'max:99999'],
            'status'           => ['required', 'in:draft,published'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul buku wajib diisi.',
            'description.required' => 'Deskripsi buku wajib diisi.',
            'author.required'      => 'Nama penulis wajib diisi.',
            'price.required'       => 'Harga wajib diisi.',
            'type.required'        => 'Tipe buku wajib dipilih.',
            'type.in'              => 'Tipe buku tidak valid.',
            'file_path.mimes'      => 'File buku harus berformat PDF.',
            'file_path.max'        => 'Ukuran file maksimal 50MB.',
            'cover_image.image'    => 'Cover harus berupa gambar.',
            'cover_image.max'      => 'Ukuran cover maksimal 2MB.',
            'status.required'      => 'Status wajib dipilih.',
            'discount_price.lt'    => 'Harga diskon harus lebih kecil dari harga normal.',
        ];
    }
}
