<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type'          => ['required', 'in:course,bootcamp,book,membership'],
            'id'            => ['required', 'integer', 'min:1'],
            'variant_id'    => ['nullable', 'integer', 'exists:course_variants,id'],
            'quantity'      => ['nullable', 'integer', 'min:1', 'max:99'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'      => 'Tipe item wajib diisi.',
            'type.in'            => 'Tipe item tidak valid.',
            'id.required'        => 'ID item wajib diisi.',
            'variant_id.exists'  => 'Varian kursus tidak ditemukan.',
        ];
    }
}
