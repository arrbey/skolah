<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'min:3', 'max:100'],
            'background_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
            'name_x'              => ['required', 'numeric', 'min:0', 'max:100'],
            'name_y'              => ['required', 'numeric', 'min:0', 'max:100'],
            'name_font_size'      => ['required', 'integer', 'min:8', 'max:120'],
            'name_font_color'     => ['required', 'string', 'max:20'],
            'name_align'          => ['required', 'in:left,center,right'],
            'name_bold'           => ['nullable', 'boolean'],
            'course_x'            => ['required', 'numeric', 'min:0', 'max:100'],
            'course_y'            => ['required', 'numeric', 'min:0', 'max:100'],
            'course_font_size'    => ['required', 'integer', 'min:8', 'max:80'],
            'course_font_color'   => ['required', 'string', 'max:20'],
            'course_align'        => ['required', 'in:left,center,right'],
            'course_bold'         => ['nullable', 'boolean'],
            'show_cert_number'    => ['nullable', 'boolean'],
            'cert_num_x'          => ['required', 'numeric', 'min:0', 'max:100'],
            'cert_num_y'          => ['required', 'numeric', 'min:0', 'max:100'],
            'cert_num_font_size'  => ['required', 'integer', 'min:6', 'max:40'],
            'cert_num_font_color' => ['required', 'string', 'max:20'],
            'show_date'           => ['nullable', 'boolean'],
            'date_x'              => ['required', 'numeric', 'min:0', 'max:100'],
            'date_y'              => ['required', 'numeric', 'min:0', 'max:100'],
            'date_font_size'      => ['required', 'integer', 'min:6', 'max:40'],
            'date_font_color'     => ['required', 'string', 'max:20'],
            'set_active'          => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'Nama template wajib diisi.',
            'name.max'                 => 'Nama template maksimal 100 karakter.',
            'background_image.image'   => 'File harus berupa gambar.',
            'background_image.mimes'   => 'Format background harus JPG atau PNG.',
            'background_image.max'     => 'Ukuran background maksimal 10 MB.',
        ];
    }
}
