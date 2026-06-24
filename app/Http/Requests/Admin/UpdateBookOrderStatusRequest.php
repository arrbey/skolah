<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'shipping_status' => ['required', 'in:pending,processing,shipped,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'courier'         => ['nullable', 'in:jne,jnt'],
            'note'            => ['nullable', 'string', 'max:500'],
        ];

        // Jika status shipped, resi & kurir wajib
        if ($this->input('shipping_status') === 'shipped') {
            $rules['tracking_number'] = ['required', 'string', 'max:100'];
            $rules['courier']         = ['required', 'in:jne,jnt'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'shipping_status.required'  => 'Status pengiriman wajib dipilih.',
            'shipping_status.in'        => 'Status pengiriman tidak valid.',
            'tracking_number.required'  => 'Nomor resi wajib diisi untuk status "Dikirim".',
            'tracking_number.max'       => 'Nomor resi maksimal 100 karakter.',
            'courier.required'          => 'Jasa pengiriman wajib dipilih untuk status "Dikirim".',
            'courier.in'               => 'Jasa pengiriman tidak valid.',
            'note.max'                  => 'Catatan maksimal 500 karakter.',
        ];
    }
}
