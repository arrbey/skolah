<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Midtrans Snap Payment Gateway.
    | Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia.
    |
    | Semua value dibaca dari .env — JANGAN hardcode credential di sini.
    |
    */

    'server_key'    => env('MIDTRANS_SERVER_KEY'),

    'client_key'    => env('MIDTRANS_CLIENT_KEY'),

    'merchant_id'   => env('MIDTRANS_MERCHANT_ID'),

    'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),

    'snap_url'      => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN)
                        ? 'https://app.midtrans.com/snap/snap.js'
                        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    /*
    |--------------------------------------------------------------------------
    | Midtrans Options
    |--------------------------------------------------------------------------
    |
    | is_sanitized  → Midtrans akan membersihkan input secara otomatis
    | is_3ds        → Aktifkan verifikasi 3D Secure untuk kartu kredit
    |
    */

    'is_sanitized'  => true,

    'is_3ds'        => true,

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Aktifkan IP whitelist untuk webhook endpoint.
    | Set false di .env untuk development/sandbox.
    |
    */

    'ip_whitelist_enabled' => filter_var(env('MIDTRANS_IP_WHITELIST', true), FILTER_VALIDATE_BOOLEAN),

];
