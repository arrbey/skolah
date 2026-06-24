<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function faq()
    {
        $faqs = [
            'Umum' => [
                [
                    'q' => 'Apa itu Skolah.com?',
                    'a' => 'Skolah.com adalah platform edukasi digital terdepan di Indonesia yang menyediakan berbagai kursus online, bootcamp intensif, dan buku digital berkualitas untuk membantu kamu meningkatkan skill di industri teknologi dan kreatif.'
                ],
                [
                    'q' => 'Bagaimana cara mendaftar di Skolah.com?',
                    'a' => 'Kamu bisa mendaftar dengan mengklik tombol "Daftar" di pojok kanan atas, lalu mengisi data diri atau menggunakan akun Google/Facebook untuk pendaftaran yang lebih cepat.'
                ],
            ],
            'Pembayaran' => [
                [
                    'q' => 'Metode pembayaran apa saja yang tersedia?',
                    'a' => 'Kami mendukung berbagai metode pembayaran mulai dari Virtual Account (BCA, Mandiri, BNI), E-Wallet (GoPay, OVO, Dana, LinkAja), hingga QRIS.'
                ],
                [
                    'q' => 'Apakah pembayaran di Skolah.com aman?',
                    'a' => 'Sangat aman. Kami menggunakan gerbang pembayaran Midtrans yang sudah terenkripsi dan diawasi oleh Bank Indonesia untuk memastikan semua transaksi kamu terlindungi.'
                ],
            ],
            'Kursus & Sertifikat' => [
                [
                    'q' => 'Apakah saya akan mendapatkan sertifikat?',
                    'a' => 'Ya! Setiap kursus atau bootcamp yang kamu selesaikan hingga 100% dan lulus ujian akhir akan mendapatkan Sertifikat Kelulusan resmi dari Skolah.com.'
                ],
                [
                    'q' => 'Apakah kursus dapat diakses selamanya?',
                    'a' => 'Ya, sekali kamu membeli kursus, kamu mendapatkan akses selamanya (Lifetime Access) termasuk pembaruan materi di masa mendatang tanpa biaya tambahan.'
                ],
            ],
        ];

        return view('pages.help.faq', compact('faqs'));
    }
}
