<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'min:5', 'max:255'],
            'description'      => ['required', 'string', 'min:20', 'max:50000'],
            'thumbnail'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price'            => ['required', 'integer', 'min:0', 'max:99999999'],
            'discount_price'   => ['nullable', 'integer', 'min:0', 'max:99999999', 'lt:price'],
            'level'            => ['required', 'in:beginner,intermediate,advanced'],
            'language'         => ['nullable', 'string', 'max:50'],
            'category_id'      => ['nullable', 'integer', 'exists:categories,id'],
            'institution_id'   => ['nullable', 'integer', 'exists:institutions,id'],
            'status'           => ['required', 'in:draft,published'],
            'is_featured'      => ['nullable', 'boolean'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul kursus wajib diisi.',
            'description.required' => 'Deskripsi kursus wajib diisi.',
            'thumbnail.image'      => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes'      => 'Format thumbnail: JPG, JPEG, PNG, atau WebP.',
            'thumbnail.max'        => 'Ukuran thumbnail maksimal 2MB.',
            'price.required'       => 'Harga wajib diisi.',
            'discount_price.lt'    => 'Harga diskon harus lebih kecil dari harga normal.',
            'level.required'       => 'Level kursus wajib dipilih.',
            'status.required'      => 'Status wajib dipilih.',
        ];
    }
}
