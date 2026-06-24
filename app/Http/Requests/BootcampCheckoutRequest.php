<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BootcampCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'bootcamp_id' => ['required', 'integer', 'exists:bootcamps,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'bootcamp_id.required' => 'Bootcamp wajib dipilih.',
            'bootcamp_id.exists'   => 'Bootcamp tidak ditemukan.',
        ];
    }
}
