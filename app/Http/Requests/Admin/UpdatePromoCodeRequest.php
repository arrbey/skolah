<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'            => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('promo_codes', 'code')->ignore($this->route('promoCode'))],
            'discount_type'   => ['required', 'in:percent,fixed'],
            'discount_value'  => ['required', 'integer', 'min:1', 'max:99999999'],
            'applicable_type' => ['required', 'in:all,course,bootcamp,book,membership,membership_monthly,membership_yearly'],
            'min_purchase'    => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'max_uses'        => ['nullable', 'integer', 'min:1', 'max:999999'],
            'expires_at'      => ['nullable', 'date'],
            'is_active'       => ['nullable', 'boolean'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->discount_type === 'percent' && $this->discount_value > 100) {
                $validator->errors()->add('discount_value', 'Diskon persen tidak boleh lebih dari 100%.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'code.required'       => 'Kode promo wajib diisi.',
            'code.unique'         => 'Kode promo sudah ada.',
            'code.alpha_dash'     => 'Kode promo hanya boleh huruf, angka, dash, dan underscore.',
            'discount_type.in'    => 'Tipe diskon harus percent atau fixed.',
            'discount_value.min'  => 'Nilai diskon minimal 1.',
        ];
    }
}
