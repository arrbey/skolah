<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'min:3', 'max:255'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'price_monthly' => ['required', 'integer', 'min:0', 'max:99999999'],
            'price_yearly'  => ['required', 'integer', 'min:0', 'max:99999999'],
            'features_text' => ['nullable', 'string', 'max:5000'],
            'promo_code_id' => ['nullable', 'integer', 'exists:promo_codes,id'],
            'is_popular'    => ['nullable', 'boolean'],
            'is_active'     => ['nullable', 'boolean'],
            'course_ids'    => ['nullable', 'array', 'max:500'],
            'course_ids.*'  => ['integer', 'exists:courses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Nama paket wajib diisi.',
            'price_monthly.required' => 'Harga bulanan wajib diisi.',
            'price_yearly.required'  => 'Harga tahunan wajib diisi.',
        ];
    }
}
