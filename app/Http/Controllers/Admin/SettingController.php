<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Requests\Admin\UploadFaviconRequest;
use App\Http\Requests\Admin\UploadLogoRequest;
use App\Models\Setting;
use App\Services\MinioStorageService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    protected $storage;
    protected $midtrans;

    public function __construct(MinioStorageService $storage, MidtransService $midtrans)
    {
        $this->storage = $storage;
        $this->midtrans = $midtrans;
    }
    /**
     * Daftar semua setting keys per group beserta default-nya.
     * Ini memastikan form selalu tampil meskipun DB kosong.
     */
    protected array $schema = [
        'general' => [
            'site_name'            => 'Skolah.com',
            'site_tagline'         => 'Platform Edukasi Digital Terlengkap di Indonesia',
            'site_description'     => 'Skolah.com adalah platform edukasi digital yang menyediakan kursus online, bootcamp, dan buku berkualitas tinggi.',
            'site_email'           => 'admin@skolah.com',
            'site_phone'           => '+62 812-3456-7890',
            'site_whatsapp'        => '+62 812-3456-7890',
            'site_address'         => 'Jakarta, Indonesia',
            'copyright_text'       => '© 2026 Skolah.com. All rights reserved.',
            'maintenance_message'  => 'Sedang dalam pemeliharaan. Kami akan kembali segera!',
        ],
        'seo' => [
            'meta_title'           => 'Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia',
            'meta_description'     => 'Belajar online bersama ribuan instruktur terbaik. Kursus, bootcamp, dan buku berkualitas di Skolah.com.',
            'meta_keywords'        => 'kursus online, belajar online, bootcamp, edukasi digital, indonesia',
            'google_analytics_id'  => '',
            'google_tag_manager'   => '',
            'facebook_pixel_id'    => '',
            'robots_txt'           => 'User-agent: *\nAllow: /',
        ],
        'social' => [
            'facebook_url'         => '',
            'instagram_url'        => '',
            'twitter_url'          => '',
            'youtube_url'          => '',
            'tiktok_url'           => '',
            'linkedin_url'         => '',
            'telegram_url'         => '',
        ],
        'payment' => [
            'midtrans_merchant_id' => '',
            'currency'             => 'IDR',
            'min_withdrawal'       => '100000',
            'instructor_commission'=> '70',
            'platform_fee'         => '30',
            'free_course_limit'    => '3',
            'payment_expiry_hours' => '24',
        ],
        'email' => [
            'mail_host'            => '',
            'mail_port'            => '465',
            'mail_username'        => '',
            'mail_from_name'       => 'Skolah.com',
            'mail_from_address'    => 'noreply@skolah.com',
            'mail_footer_text'     => 'Email ini dikirim otomatis oleh sistem Skolah.com.',
        ],
        'maintenance' => [
            'maintenance_mode'     => '0',
            'cache_lifetime'       => '3600',
            'registration_open'    => '1',
            'course_review_open'   => '1',
            'max_file_upload_mb'   => '10',
            'max_uploads_per_hour' => '20',
        ],
        'landing' => [
            'hero_title_accent'    => 'Platform EdTech #1 Indonesia',
            'hero_title_main'      => 'Tingkatkan Skill Kariermu Hari Ini.',
            'hero_description'     => 'Akses ribuan kursus online, bootcamp interaktif, dan buku digital dari praktisi industri terbaik. Saatnya wujudkan karir impianmu.',
            'landing_benefit_title'    => 'Rintis Karir Bersama Skolah.com',
            'landing_benefit_subtitle' => 'Platform edukasi terlengkap untuk membantu kamu meraih karir impian di industri digital.',
            'landing_program_title'    => 'Pilih Cara Belajar Terbaik Untukmu',
            'landing_gallery_title'    => 'Rasanya Gabung Komunitas Skolah.com',
            'landing_gallery_subtitle' => 'Intip keseruan teman-teman komunitas dalam berbagai kegiatan pengembangan diri.',
        ],
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Bangun data settings dengan nilai dari DB, fallback ke schema default
        $settings = [];
        foreach ($this->schema as $group => $keys) {
            $settings[$group] = [];
            foreach ($keys as $key => $default) {
                $settings[$group][$key] = Setting::get($key, $default);
            }
        }

        // Info server
        $serverInfo = [
            'php_version'      => phpversion(),
            'laravel_version'  => app()->version(),
            'cache_driver'     => config('cache.default'),
            'queue_driver'     => config('queue.default'),
            'disk_usage'       => $this->getDiskUsage(),
            'db_size'          => $this->getDbSize(),
            'storage_writable' => is_writable(storage_path()),
            'cache_size'       => $this->getCacheFileCount(),
        ];

        // Ambil preferensi Midtrans
        $midtransPrefs = $this->midtrans->getMerchantPreferences();

        return view('admin.settings.index', compact('settings', 'serverInfo', 'midtransPrefs'));
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function update(UpdateSettingRequest $request)
    {
        $group = $request->validated('group', 'general');

        if (! array_key_exists($group, $this->schema)) {
            return back()->with('error', 'Group pengaturan tidak valid.');
        }

        $data = $request->input('settings', []);

        foreach ($this->schema[$group] as $key => $default) {
            $value = $data[$key] ?? '';
            // Khusus checkbox (toggle): jika tidak ada di request, set ke '0'
            if (in_array($key, ['maintenance_mode', 'registration_open', 'course_review_open'])) {
                $value = isset($data[$key]) ? '1' : '0';
            }
            Setting::set($key, $value, $group);
        }

        // Flush semua cache
        Cache::flush();

        return back()->with('success', 'Pengaturan ' . ucfirst($group) . ' berhasil disimpan.');
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function uploadLogo(UploadLogoRequest $request)
    {

        // Verifikasi MIME server-side (bukan berdasarkan ekstensi)
        $file = $request->file('logo');
        $realMime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        if (! in_array($realMime, ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'])) {
            return back()->withErrors(['logo' => 'Tipe file tidak diizinkan.']);
        }

        $oldLogo = Setting::get('site_logo');
        if ($oldLogo) {
            $this->storage->delete($oldLogo);
        }

        $path = $this->storage->uploadBanner($file);

        Setting::set('site_logo', $path, 'general');
        Cache::forget('setting.site_logo');

        return back()->with('success', 'Logo berhasil diperbarui.');
    }

    public function uploadFavicon(UploadFaviconRequest $request)
    {
        // Verifikasi MIME server-side
        $file = $request->file('favicon');
        $realMime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        if (! in_array($realMime, ['image/jpeg', 'image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/webp'])) {
            return back()->withErrors(['favicon' => 'Tipe file tidak diizinkan.']);
        }

        $oldFav = Setting::get('site_favicon');
        if ($oldFav) {
            $this->storage->delete($oldFav);
        }

        $path = $this->storage->uploadBanner($file);

        Setting::set('site_favicon', $path, 'general');
        Cache::forget('setting.site_favicon');

        return back()->with('success', 'Favicon berhasil diperbarui.');
    }

    public function clearCache(Request $request)
    {
        try {
            Cache::flush();
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            return back()->with('success', 'Cache berhasil dibersihkan.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Cache clear failed', [
                'admin_id' => auth()->id(),
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal membersihkan cache. Cek log untuk detail.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function getDiskUsage(): string
    {
        try {
            $bytes = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            if ($bytes === false || $total === false || $total === 0) return '-';
            $used   = $total - $bytes;
            $pct    = round(($used / $total) * 100);
            return $this->formatBytes($used) . ' / ' . $this->formatBytes($total) . " ({$pct}%)";
        } catch (\Throwable) {
            return '-';
        }
    }

    private function getDbSize(): string
    {
        try {
            $db   = config('database.connections.mysql.database');
            $rows = \Illuminate\Support\Facades\DB::select(
                "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.tables WHERE table_schema = ?",
                [$db]
            );
            $size = $rows[0]->size ?? 0;
            return $size . ' MB';
        } catch (\Throwable) {
            return '-';
        }
    }

    private function getCacheFileCount(): string
    {
        try {
            $path  = storage_path('framework/cache/data');
            $count = iterator_count(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)));
            return number_format($count) . ' file';
        } catch (\Throwable) {
            return '0 file';
        }
    }

    private function formatBytes(int|float $bytes): string
    {
        if ($bytes >= 1_073_741_824) return round($bytes / 1_073_741_824, 1) . ' GB';
        if ($bytes >= 1_048_576)     return round($bytes / 1_048_576, 1) . ' MB';
        if ($bytes >= 1_024)         return round($bytes / 1_024, 1) . ' KB';
        return $bytes . ' B';
    }
}
