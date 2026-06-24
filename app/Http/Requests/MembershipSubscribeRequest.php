<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MembershipSubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'plan_id'       => ['required', 'integer', 'exists:membership_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required'       => 'Pilih paket membership.',
            'plan_id.exists'         => 'Paket membership tidak ditemukan.',
            'billing_cycle.required' => 'Siklus pembayaran wajib dipilih.',
            'billing_cycle.in'       => 'Siklus pembayaran harus monthly atau yearly.',
        ];
    }
}
