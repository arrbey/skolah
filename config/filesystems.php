<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/temp'), // Hanya untuk file sementara
        ],

        // ── Backup disk (local redundancy, fast restore) ──────────────────
        'backups' => [
            'driver'     => 'local',
            'root'       => storage_path('app/backups'),
            'throw'      => false,
            'visibility' => 'private',
        ],

        // ── Backup off-site (MinIO bucket terpisah) ───────────────────────
        // Aktifkan dengan BACKUP_ENABLE_REMOTE=true di .env
        'backup_s3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket'                  => env('BACKUP_S3_BUCKET', 'skolah-backup'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'visibility'              => 'private',
            'throw'                   => false,
            'options' => [
                'http' => [
                    'verify' => false,
                ],
            ],
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => rtrim(env('APP_URL', 'http://localhost'), '/') . '/storage',
            'visibility' => 'public',
            'throw'      => false,
        ],

        // ── MinIO s3.morrbali.com (S3-compatible) ─────────────────────────
        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket'                  => env('AWS_BUCKET', 'public'),
            'url'                     => env('MINIO_PUBLIC_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'visibility'              => 'public',
            'throw'                   => true,
            'options' => [
                'http' => [
                    'verify' => false, // Bypass SSL verification jika hosting memblokir CA bundles
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

