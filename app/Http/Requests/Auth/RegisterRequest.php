<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class RegisterRequest extends FormRequest
{
    protected array $blockedEmailDomains = [
        '10minutemail.com',
        '20minutemail.com',
        '33mail.com',
        'dispostable.com',
        'emailondeck.com',
        'fakeinbox.com',
        'guerrillamail.com',
        'mailinator.com',
        'maildrop.cc',
        'moakt.com',
        'sharklasers.com',
        'temp-mail.org',
        'tempmail.com',
        'throwawaymail.com',
        'trashmail.com',
        'yopmail.com',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\-\.]+$/u'],
            'email'              => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password'           => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
            'terms'              => ['required', 'accepted'],
            'register_form_token' => ['required', 'string', 'size:40'],
            'register_js_token'   => ['required', 'string', 'size:64'],
            'g-recaptcha-response' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $loadedAt = (int) $this->session()->get('register_form_loaded_at', 0);
            $sessionToken = (string) $this->session()->get('register_form_token', '');
            $formToken = (string) $this->input('register_form_token', '');
            $expectedJsToken = (string) $this->session()->get('register_js_token', '');
            $jsToken = (string) $this->input('register_js_token', '');

            if ($loadedAt === 0 || now()->timestamp - $loadedAt < 4) {
                $validator->errors()->add('email', 'Registrasi terlalu cepat. Silakan coba lagi.');
            }

            if ($sessionToken === '' || ! hash_equals($sessionToken, $formToken)) {
                $validator->errors()->add('email', 'Sesi registrasi tidak valid. Muat ulang halaman.');
            }

            if ($expectedJsToken === '' || ! hash_equals($expectedJsToken, $jsToken)) {
                $validator->errors()->add('email', 'Validasi browser gagal. Muat ulang halaman dan coba lagi.');
            }

            // ── Verifikasi Google reCAPTCHA v3 ──────────────────────────────
            $recaptchaToken = (string) $this->input('g-recaptcha-response', '');
            if ($recaptchaToken !== '' && config('recaptchav3.secret') !== '') {
                try {
                    $score = RecaptchaV3::verify($recaptchaToken, 'register');
                    if ($score === false || $score < 0.5) {
                        $validator->errors()->add('email', 'Verifikasi bot gagal. Silakan coba lagi.');
                    }
                } catch (\Exception $e) {
                    // Jika reCAPTCHA API gagal (misal: tidak ada internet), biarkan lolos
                    // agar user asli tidak terblokir saat server Google down
                    \Illuminate\Support\Facades\Log::warning('reCAPTCHA verify failed: ' . $e->getMessage());
                }
            }

            $domain = Str::lower(Str::after((string) $this->input('email'), '@')); 
            if (in_array($domain, $this->blockedEmailDomains, true)) {
                $validator->errors()->add('email', 'Gunakan alamat email aktif, bukan email sementara.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'Nama lengkap wajib diisi.',
            'name.min'                    => 'Nama minimal 3 karakter.',
            'name.regex'                  => 'Nama hanya boleh mengandung huruf, spasi, titik, dan tanda hubung.',
            'email.required'              => 'Email wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'email.unique'                => 'Email ini sudah terdaftar. Silakan login.',
            'password.required'           => 'Password wajib diisi.',
            'password.confirmed'          => 'Konfirmasi password tidak cocok.',
            'password.max'                => 'Password maksimal 72 karakter.',
            'terms.required'              => 'Anda harus menyetujui syarat dan ketentuan.',
            'terms.accepted'              => 'Anda harus menyetujui syarat dan ketentuan.',
            'register_form_token.required' => 'Sesi registrasi tidak valid. Muat ulang halaman.',
            'register_js_token.required'   => 'Validasi browser gagal. Muat ulang halaman dan coba lagi.',
            'g-recaptcha-response.required' => 'Verifikasi reCAPTCHA gagal. Muat ulang halaman dan coba lagi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama lengkap',
            'email'    => 'email',
            'password' => 'password',
            'terms'    => 'syarat dan ketentuan',
        ];
    }
}
