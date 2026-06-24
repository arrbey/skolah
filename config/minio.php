<?php

// config/minio.php
// Konfigurasi MinIO Object Storage untuk Skolah.com

return [

    // URL base untuk file PUBLIC (gambar — langsung bisa diakses browser)
    'public_url' => env('MINIO_PUBLIC_URL', 'https://s3.morrbali.com/public'),

    // Durasi Signed URL dalam MENIT (untuk file private)
    'expiry' => [
        'video'       => (int) env('MINIO_VIDEO_EXPIRY', 120), // Video LMS: 2 jam
        'book'        => (int) env('MINIO_BOOK_EXPIRY',  15),  // Buku digital: 15 menit
        'certificate' => (int) env('MINIO_CERT_EXPIRY',  30),  // Sertifikat: 30 menit
        'default'     => 10,
    ],

    // Path prefix per jenis file di dalam bucket
    // PUBLIC → URL langsung bisa diakses
    // PRIVATE → hanya via signed URL
    'paths' => [
        // ── PUBLIC ────────────────────────────────────────────────────────
        'course_thumbnail'   => 'images/courses',
        'user_avatar'        => 'images/users',
        'banner'             => 'images/banners',
        'book_cover'         => 'images/books',
        'bootcamp_thumbnail' => 'images/bootcamps',
        'category_icon'      => 'images/categories',
        'testimonial_photo'  => 'images/testimonials',
        'delivery_photo'     => 'images/delivery-photos',

        // ── PRIVATE ───────────────────────────────────────────────────────
        'lms_video'          => 'videos/lms',
        'book_file'          => 'books',
        'certificate'        => 'certificates',
    ],
];
