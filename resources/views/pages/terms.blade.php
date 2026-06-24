@extends('layouts.app')
@section('title', 'Syarat & Ketentuan' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-600 pt-28 pb-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-4xl font-bold text-white mb-3">Syarat & Ketentuan</h1>
        <p class="text-white/70 text-sm">Terakhir diperbarui: 1 Januari 2025</p>
    </div>
</section>

<section class="bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 prose prose-gray max-w-none">

            <h2>1. Penerimaan Syarat</h2>
            <p>Dengan mengakses atau menggunakan layanan {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}, Anda menyetujui untuk terikat dengan Syarat & Ketentuan ini. Jika Anda tidak setuju dengan ketentuan ini, harap tidak menggunakan layanan kami.</p>

            <h2>2. Akun Pengguna</h2>
            <p>Untuk mengakses fitur tertentu, Anda perlu membuat akun. Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun Anda dan semua aktivitas yang terjadi di bawah akun tersebut.</p>

            <h2>3. Pembayaran & Pengembalian Dana</h2>
            <p>Semua transaksi diproses melalui Midtrans. Pembayaran bersifat final kecuali dalam kasus kesalahan teknis dari pihak kami. Pengembalian dana dapat diajukan dalam 7 hari setelah pembelian jika belum mengakses konten.</p>

            <h2>4. Hak Kekayaan Intelektual</h2>
            <p>Semua konten di platform {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}, termasuk namun tidak terbatas pada video, teks, gambar, dan materi pembelajaran, dilindungi oleh hak cipta dan hak kekayaan intelektual lainnya.</p>

            <h2>5. Larangan Penggunaan</h2>
            <p>Pengguna dilarang untuk:</p>
            <ul>
                <li>Menyebarkan atau menjual kembali konten platform</li>
                <li>Menggunakan platform untuk kegiatan ilegal</li>
                <li>Mengunggah konten yang melanggar hak orang lain</li>
                <li>Melakukan tindakan yang dapat merusak sistem</li>
            </ul>

            <h2>6. Perubahan Layanan</h2>
            <p>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }} berhak mengubah, menangguhkan, atau menghentikan layanan kapan saja tanpa pemberitahuan sebelumnya. Kami tidak bertanggung jawab atas kerugian yang mungkin timbul dari perubahan tersebut.</p>

            <h2>7. Batasan Tanggung Jawab</h2>
            <p>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }} tidak bertanggung jawab atas kerugian langsung, tidak langsung, atau konsekuensial yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan kami.</p>

            <h2>8. Hukum yang Berlaku</h2>
            <p>Syarat & Ketentuan ini diatur oleh hukum Republik Indonesia. Setiap sengketa akan diselesaikan melalui pengadilan yang berwenang di Jakarta.</p>

            <h2>9. Hubungi Kami</h2>
            <p>Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini, silakan hubungi kami di <a href="mailto:{{\App\Models\Setting::get('site_email', 'legal@skolah.com')}}" class="text-primary-600">{{\App\Models\Setting::get('site_email', 'legal@skolah.com')}}</a>.</p>

        </div>
    </div>
</section>

@endsection
