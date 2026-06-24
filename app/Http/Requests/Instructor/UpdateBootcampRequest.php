<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBootcampRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id'   => ['nullable', 'exists:institutions,id'],
            'title'            => ['required', 'string', 'min:5', 'max:255'],
            'description'      => ['required', 'string', 'min:20', 'max:50000'],
            'thumbnail'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price'            => ['required', 'integer', 'min:0', 'max:99999999'],
            'discount_price'   => ['nullable', 'integer', 'min:0', 'max:99999999', 'lt:price'],
            'type'             => ['required', 'in:online,offline'],
            'platform'         => ['nullable', 'string', 'max:100'],
            'meeting_link'     => ['nullable', 'string', 'max:1000'],
            'location'         => ['nullable', 'string', 'max:500'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'max_participants' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'status'           => ['required', 'in:upcoming,ongoing,completed'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Judul bootcamp wajib diisi.',
            'description.required'    => 'Deskripsi bootcamp wajib diisi.',
            'price.required'          => 'Harga wajib diisi.',
            'type.required'           => 'Tipe bootcamp wajib dipilih.',
            'start_date.required'     => 'Tanggal mulai wajib diisi.',
            'end_date.required'       => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required'         => 'Status wajib dipilih.',
            'discount_price.lt'       => 'Harga diskon harus lebih kecil dari harga normal.',
        ];
    }
}
