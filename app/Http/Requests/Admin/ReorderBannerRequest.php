<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReorderBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order'   => ['required', 'array', 'min:1', 'max:100'],
            'order.*' => ['required', 'integer', 'exists:banners,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'order.required' => 'Data urutan wajib dikirim.',
            'order.array'    => 'Data urutan harus berupa array.',
        ];
    }
}
