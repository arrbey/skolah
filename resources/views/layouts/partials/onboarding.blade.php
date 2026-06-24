{{-- Driver.js Assets --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
<script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>

<style>
    .driver-popover.skolah-tour {
        background-color: #ffffff;
        color: #1e293b;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 2px solid #3b82f6;
        max-width: 300px;
    }
    .driver-popover.skolah-tour .driver-popover-title {
        font-weight: 900;
        font-size: 1.25rem;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .driver-popover.skolah-tour .driver-popover-description {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.5;
    }
    .driver-popover.skolah-tour .driver-popover-footer button {
        background-color: #0f172a;
        color: white;
        text-shadow: none;
        border: none;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 12px;
        transition: all 0.2s;
    }
    .driver-popover.skolah-tour .driver-popover-footer button:hover {
        background-color: #3b82f6;
    }
</style>

<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', () => {
    const user = @json(auth()->user());
    if (!user) return;

    const driver = window.driver.js.driver;
    const currentRoute = "{{ Route::currentRouteName() }}";
    const seenPages = user.seen_onboarding_pages || [];

    // Jika user sudah menyelesaikan semua tutorial besar, jangan tampilkan lagi
    if (user.has_seen_onboarding) return;

    let tourSteps = [];

    // ─── 0. TOUR HOMEPAGE ────────────────────────────────────────────────────
    if (currentRoute === 'home' && !seenPages.includes('home')) {
        tourSteps = [
            { element: '#nav-home-courses', popover: { title: '📚 Katalog Kursus', description: 'Temukan ribuan kursus pilihan dari berbagai bidang keahlian di sini.', position: 'bottom' } },
            { element: '#nav-home-bootcamps', popover: { title: '🚀 Program Bootcamp', description: 'Ingin karir melesat? Ikuti pelatihan intensif bersama mentor ahli kami.', position: 'bottom' } },
            { element: '#nav-home-books', popover: { title: '📖 Toko Buku', description: 'Lengkapi referensimu dengan koleksi buku fisik dan digital eksklusif.', position: 'bottom' } },
            { element: '#nav-home-register', popover: { title: '✨ Daftar Sekarang', description: 'Ayo mulai perjalanan belajarmu hari ini dengan mendaftar gratis!', position: 'bottom' } }
        ];
    }
    // ─── 0.1 TOUR HALAMAN KURSUS PUBLIK ──────────────────────────────────────
    else if (currentRoute === 'courses.index' && !seenPages.includes('courses.index')) {
        tourSteps = [
            { 
                element: 'main h1', 
                popover: { 
                    title: '🔍 Jelajahi Kursus', 
                    description: 'Selamat datang di katalog kursus! Di sini ada ribuan materi belajar siap kamu pelajari.', 
                    position: 'bottom' 
                } 
            },
            { 
                element: '#course-filters', 
                popover: { 
                    title: '🏷️ Filter Pintar', 
                    description: 'Gunakan panel ini untuk memfilter kursus berdasarkan Kategori, Level (Pemula - Mahir), atau Harga (Gratis/Berbayar).', 
                    position: 'right' 
                } 
            },
            { 
                element: '#course-list', 
                popover: { 
                    title: '📺 Daftar Kursus', 
                    description: 'Hasil pencarianmu akan muncul di sini. Klik pada kartu kursus untuk melihat detail materi dan instruktur.', 
                    position: 'top' 
                } 
            }
        ];
    }
    // ─── 0.2 TOUR HALAMAN BOOTCAMP PUBLIK ────────────────────────────────────
    else if (currentRoute === 'bootcamps.index' && !seenPages.includes('bootcamps.index')) {
        tourSteps = [
            { 
                element: '#bootcamp-header', 
                popover: { 
                    title: '🚀 Program Akselerasi', 
                    description: 'Bootcamp adalah pelatihan intensif untuk membantumu siap kerja dalam waktu singkat.', 
                    position: 'bottom' 
                } 
            },
            { 
                element: '#bootcamp-content', 
                popover: { 
                    title: '📅 Jadwal & Pendaftaran', 
                    description: 'Cek jadwal mulai dan kurikulum setiap bootcamp di bawah ini. Jangan sampai kehabisan kuota!', 
                    position: 'top' 
                } 
            }
        ];
    }
    // ─── 0.3 TOUR HALAMAN BUKU PUBLIK ────────────────────────────────────────
    else if (currentRoute === 'books.index' && !seenPages.includes('books.index')) {
        tourSteps = [
            { 
                element: '#book-store-title', 
                popover: { 
                    title: '📖 Skolah Bookstore', 
                    description: 'Lengkapi belajarmu dengan buku-buku eksklusif dari para ahli.', 
                    position: 'bottom' 
                } 
            },
            { 
                element: '#book-content', 
                popover: { 
                    title: '📦 Digital vs Fisik', 
                    description: 'Kamu bisa memilih format E-Book (langsung baca) atau Buku Fisik (dikirim ke rumah). Gunakan filter untuk memilih formatnya.', 
                    position: 'top' 
                } 
            }
        ];
    }
    // ─── 1. TOUR DASHBOARD ───────────────────────────────────────────────────
    else if (currentRoute === 'dashboard' && !seenPages.includes('dashboard')) {
        tourSteps = [
            { element: '#dashboard-welcome', popover: { title: '👋 Halo, ' + user.name + '!', description: 'Selamat datang! Mari kita mulai tur lengkap navigasi Skolah.com. Klik Next.', position: 'bottom' } },
            { element: '#nav-my-courses', popover: { title: '🚀 Misi 1: Kursus Saya', description: 'Klik menu ini untuk melihat koleksi kursus yang kamu miliki.', position: 'right' } }
        ];
    } 
    // ─── 2. TOUR MY COURSES ──────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.my-courses' && !seenPages.includes('dashboard.my-courses')) {
        tourSteps = [
            { element: 'main', popover: { title: '🎓 Halaman Kursus', description: 'Bagus! Di sini semua materi belajarmu tersimpan. Klik Next.', position: 'bottom' } },
            { element: '#nav-my-bootcamps', popover: { title: '🚀 Misi 2: Bootcamp', description: 'Sekarang, silakan KLIK menu Bootcamp untuk melihat jadwal pelatihan intensif.', position: 'right' } }
        ];
    }
    // ─── 3. TOUR BOOTCAMPS ───────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.my-bootcamps' && !seenPages.includes('dashboard.my-bootcamps')) {
        tourSteps = [
            { element: 'main', popover: { title: '🔥 Program Bootcamp', description: 'Di sini kamu bisa memantau jadwal live session bersama mentor. Klik Next.', position: 'bottom' } },
            { element: '#nav-my-books', popover: { title: '📖 Misi 3: Koleksi Buku', description: 'Selanjutnya, silakan KLIK menu Buku Saya.', position: 'right' } }
        ];
    }
    // ─── 4. TOUR BOOKS ───────────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.my-books' && !seenPages.includes('dashboard.my-books')) {
        tourSteps = [
            { element: 'main', popover: { title: '📚 Toko Buku & E-Book', description: 'Akses referensi ilmumu di sini, baik digital maupun fisik. Klik Next.', position: 'bottom' } },
            { element: '#nav-certificates', popover: { title: '📜 Misi 4: Sertifikat', description: 'Silakan KLIK menu Sertifikat untuk melihat pencapaianmu.', position: 'right' } }
        ];
    }
    // ─── 5. TOUR CERTIFICATES ────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.certificates' && !seenPages.includes('dashboard.certificates')) {
        tourSteps = [
            { element: 'main', popover: { title: '🏅 Klaim Sertifikat', description: 'Semua sertifikat kelulusanmu bisa di-download di halaman ini. Klik Next.', position: 'bottom' } },
            { element: '#nav-chat', popover: { title: '💬 Misi 5: Chat Pesan', description: 'Ayo coba lihat fitur Chat, silakan KLIK menu Chat Pesan.', position: 'right' } }
        ];
    }
    // ─── 6. TOUR CHAT ────────────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.chat' && !seenPages.includes('dashboard.chat')) {
        tourSteps = [
            { element: 'main', popover: { title: '💬 Chat & Diskusi', description: 'Hubungi Mentor atau Admin kapan saja jika kamu punya pertanyaan. Klik Next.', position: 'bottom' } },
            { element: '#nav-orders', popover: { title: '💳 Misi 6: Riwayat Order', description: 'Terakhir sebelum selesai, silakan KLIK menu Riwayat Order.', position: 'right' } }
        ];
    }
    // ─── 7. TOUR ORDERS ──────────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.orders' && !seenPages.includes('dashboard.orders')) {
        tourSteps = [
            { element: 'main', popover: { title: '🧾 Riwayat Transaksi', description: 'Pantau status pembayaran dan download invoice belajarmu di sini. Klik Next.', position: 'bottom' } },
            { element: '#nav-settings', popover: { title: '⚙️ Misi Final: Pengaturan', description: 'Terakhir, silakan KLIK menu Pengaturan untuk mengakhiri tur ini.', position: 'right' } }
        ];
    }
    // ─── 8. TOUR SETTINGS ────────────────────────────────────────────────────
    else if (currentRoute === 'dashboard.settings' && !seenPages.includes('dashboard.settings')) {
        tourSteps = [
            { element: 'main', popover: { title: '⚙️ Profil & Keamanan', description: 'Lengkapi datamu agar nama di sertifikat akurat. Hampir selesai!', position: 'bottom' } },
            { 
                element: '#user-menu-tour', 
                popover: { 
                    title: '🌐 Kembali ke Home', 
                    description: 'Terakhir, jika kamu ingin kembali ke halaman utama website, klik profil kamu di sini lalu pilih **"Ke Halaman Utama"**. Selamat belajar!', 
                    position: 'bottom' 
                } 
            }
        ];
    }

    if (tourSteps.length > 0) {
        const d = driver({
            showProgress: true,
            popoverClass: 'skolah-tour',
            allowClose: false, // User tidak bisa asal tutup
            overlayClickAction: 'none', // Klik overlay tidak menutup tutorial
            steps: tourSteps,
            onHighlightStarted: (element, step) => {
                // Jika langkah ini menyuruh user untuk KLIK (Langkah navigasi)
                // Kita hilangkan tombol "Next" agar mereka beneran klik elemennya
                if (step.popover.description.includes('KLIK')) {
                    setTimeout(() => {
                        const nextBtn = document.querySelector('.driver-popover-next-btn');
                        if (nextBtn) nextBtn.style.display = 'none';
                    }, 10);
                }
            },
            onDestroyStarted: () => {
                finishPageTour(currentRoute);
                d.destroy();
            }
        });

        // Langsung jalankan tanpa delay lama agar user tidak perlu klik dulu
        d.drive();
    }

    async function finishPageTour(routeName) {
        try { 
            await axios.post('{{ route('dashboard.onboarding.finish') }}', {
                page: routeName
            }); 
        } catch (e) {}
    }
});
</script>

<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: rotate(0deg) translateY(0); }
    50% { transform: rotate(5deg) translateY(-10px); }
}
.animate-float {
    animation: float 4s ease-in-out infinite;
}
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.9; }
}
.animate-pulse-slow {
    animation: pulse-slow 2.5s ease-in-out infinite;
}
</style>
