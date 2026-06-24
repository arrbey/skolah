<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:3', 'max:255'],
            'phone'       => ['required', 'string', 'min:8', 'max:20', 'regex:/^[\d\+\-\s]+$/'],
            'address'     => ['required', 'string', 'min:10', 'max:500'],
            'city'        => ['required', 'string', 'min:2', 'max:100'],
            'province'    => ['required', 'string', 'min:2', 'max:100'],
            'postal_code' => ['required', 'string', 'min:3', 'max:10', 'regex:/^[\d]+$/'],
            'courier'     => ['required', 'in:jne,jnt'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama penerima wajib diisi.',
            'phone.required'       => 'Nomor telepon wajib diisi.',
            'address.required'     => 'Alamat lengkap wajib diisi.',
            'city.required'        => 'Kota wajib diisi.',
            'province.required'    => 'Provinsi wajib diisi.',
            'postal_code.required' => 'Kode pos wajib diisi.',
            'courier.required'     => 'Pilih jasa pengiriman (JNE atau J&T Express).',
            'courier.in'           => 'Jasa pengiriman tidak valid.',
        ];
    }
}
