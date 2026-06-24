@extends('layouts.app')
@section('title', 'Kebijakan Privasi' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-600 pt-28 pb-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-4xl font-bold text-white mb-3">Kebijakan Privasi</h1>
        <p class="text-white/70 text-sm">Terakhir diperbarui: 1 Januari 2025</p>
    </div>
</section>

<section class="bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 prose prose-gray max-w-none">

            <h2>1. Informasi yang Kami Kumpulkan</h2>
            <p>Kami mengumpulkan informasi yang Anda berikan secara langsung, seperti:</p>
            <ul>
                <li>Nama dan alamat email saat registrasi</li>
                <li>Informasi pembayaran (diproses oleh Midtrans, tidak kami simpan)</li>
                <li>Data aktivitas belajar dan progres kursus</li>
                <li>Ulasan dan komentar yang Anda kirimkan</li>
            </ul>

            <h2>2. Cara Kami Menggunakan Informasi</h2>
            <p>Informasi yang kami kumpulkan digunakan untuk:</p>
            <ul>
                <li>Menyediakan dan meningkatkan layanan platform</li>
                <li>Mengirim notifikasi terkait akun dan pembelian</li>
                <li>Memberikan dukungan pelanggan</li>
                <li>Menganalisis penggunaan platform untuk perbaikan</li>
            </ul>

            <h2>3. Keamanan Data</h2>
            <p>Kami mengimplementasikan langkah-langkah keamanan teknis dan organisasi yang memadai untuk melindungi data pribadi Anda dari akses tidak sah, perubahan, pengungkapan, atau penghancuran.</p>

            <h2>4. Berbagi Data dengan Pihak Ketiga</h2>
            <p>Kami tidak menjual atau menyewakan data pribadi Anda. Kami dapat berbagi data dengan:</p>
            <ul>
                <li>Penyedia layanan pembayaran (Midtrans) untuk memproses transaksi</li>
                <li>Penegak hukum jika diwajibkan oleh regulasi</li>
                <li>Instruktur (hanya nama dan progres belajar, bukan data sensitif)</li>
            </ul>

            <h2>5. Cookie</h2>
            <p>Kami menggunakan cookie untuk meningkatkan pengalaman pengguna, menyimpan preferensi, dan menganalisis traffic. Anda dapat mengatur browser untuk menolak cookie, namun beberapa fitur mungkin tidak berfungsi optimal.</p>

            <h2>6. Hak Anda</h2>
            <p>Anda memiliki hak untuk:</p>
            <ul>
                <li>Mengakses data pribadi yang kami miliki tentang Anda</li>
                <li>Meminta koreksi data yang tidak akurat</li>
                <li>Meminta penghapusan akun dan data Anda</li>
                <li>Menarik persetujuan kapan saja</li>
            </ul>

            <h2>7. Retensi Data</h2>
            <p>Kami menyimpan data Anda selama akun aktif atau diperlukan untuk menyediakan layanan. Data dapat dihapus atas permintaan Anda melalui pengaturan akun atau menghubungi tim kami.</p>

            <h2>8. Hubungi Kami</h2>
            <p>Untuk pertanyaan terkait privasi, hubungi kami di <a href="mailto:{{\App\Models\Setting::get('site_email', 'privacy@skolah.com')}}" class="text-primary-600">{{\App\Models\Setting::get('site_email', 'privacy@skolah.com')}}</a>.</p>

        </div>
    </div>
</section>

@endsection
