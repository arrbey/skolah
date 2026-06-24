<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'book_id'       => ['required', 'integer', 'exists:books,id'],
            'purchase_type' => ['required', 'in:digital,physical,both'],
            'quantity'      => ['sometimes', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required'       => 'Buku wajib dipilih.',
            'book_id.exists'         => 'Buku tidak ditemukan.',
            'purchase_type.required' => 'Tipe pembelian wajib dipilih.',
            'purchase_type.in'       => 'Tipe pembelian tidak valid.',
        ];
    }
}
