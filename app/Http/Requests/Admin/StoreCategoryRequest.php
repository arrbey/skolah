<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:2', 'max:255', 'unique:categories,name'],
            'icon'      => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'parent_id.exists' => 'Kategori induk tidak valid.',
        ];
    }
}
