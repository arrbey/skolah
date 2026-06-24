<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyPromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'promo_code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'promo_code.required' => 'Kode promo wajib diisi.',
            'promo_code.max'      => 'Kode promo terlalu panjang.',
        ];
    }
}
