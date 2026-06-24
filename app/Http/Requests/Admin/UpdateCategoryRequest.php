<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:2', 'max:255', Rule::unique('categories', 'name')->ignore($this->route('category'))],
            'icon'      => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah ada.',
            'parent_id.exists' => 'Kategori induk tidak valid.',
        ];
    }
}
