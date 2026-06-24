<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class InstructorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'motivation'    => ['required', 'string', 'min:50', 'max:2000'],
            'expertise'     => ['required', 'string', 'min:3', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'phone'         => ['nullable', 'string', 'min:8', 'max:20', 'regex:/^[\d\+\-\s]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivation.required' => 'Ceritakan motivasi kamu menjadi instruktur.',
            'motivation.min'      => 'Motivasi minimal 50 karakter.',
            'motivation.max'      => 'Motivasi maksimal 2000 karakter.',
            'expertise.required'  => 'Bidang keahlian harus diisi.',
            'expertise.max'       => 'Bidang keahlian maksimal 255 karakter.',
            'portfolio_url.url'   => 'Format URL portofolio tidak valid.',
            'portfolio_url.max'   => 'URL portofolio maksimal 500 karakter.',
            'phone.regex'         => 'Format nomor telepon tidak valid.',
        ];
    }
}
