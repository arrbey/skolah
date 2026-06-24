<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:tags,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tag wajib diisi.',
            'name.min'      => 'Nama tag minimal 2 karakter.',
            'name.max'      => 'Nama tag maksimal 100 karakter.',
            'name.unique'   => 'Tag dengan nama tersebut sudah ada.',
        ];
    }
}
