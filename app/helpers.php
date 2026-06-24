<?php

use Carbon\Carbon;

if (! function_exists('rupiah')) {
    /**
     * Format angka ke format mata uang Rupiah Indonesia.
     *
     * @example rupiah(299000) → "Rp 299.000"
     */
    function rupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (! function_exists('rupiah_short')) {
    /**
     * Format Rupiah singkat untuk tampilan kompak.
     *
     * @example rupiah_short(1500000) → "Rp 1,5 jt"
     */
    function rupiah_short(int $amount): string
    {
        if ($amount >= 1_000_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000_000, 1, ',', '.') . ' M';
        }

        if ($amount >= 1_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000, 1, ',', '.') . ' jt';
        }

        if ($amount >= 1_000) {
            return 'Rp ' . number_format($amount / 1_000, 0, ',', '.') . ' rb';
        }

        return 'Rp ' . $amount;
    }
}

if (! function_exists('tanggal_indo')) {
    /**
     * Format tanggal ke format Indonesia panjang.
     *
     * @example tanggal_indo($date) → "15 Januari 2025"
     */
    function tanggal_indo(Carbon|string $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->translatedFormat('d F Y');
    }
}

if (! function_exists('tanggal_waktu_indo')) {
    /**
     * Format tanggal + waktu ke format Indonesia.
     *
     * @example tanggal_waktu_indo($date) → "15 Januari 2025, 14:30 WIB"
     */
    function tanggal_waktu_indo(Carbon|string $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->translatedFormat('d F Y, H:i') . ' WIB';
    }
}

if (! function_exists('tanggal_singkat_indo')) {
    /**
     * Format tanggal singkat Indonesia.
     *
     * @example tanggal_singkat_indo($date) → "15 Jan 2025"
     */
    function tanggal_singkat_indo(Carbon|string $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->translatedFormat('d M Y');
    }
}

if (! function_exists('getYoutubeId')) {
    /**
     * Ambil YouTube video ID dari berbagai format URL.
     * Mendukung: watch?v=, youtu.be/, embed/
     *
     * @example getYoutubeId('https://youtu.be/dQw4w9WgXcQ') → "dQw4w9WgXcQ"
     */
    function getYoutubeId(string $url): ?string
    {
        preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $url, $matches);

        return $matches[1] ?? null;
    }
}

if (! function_exists('youtubeEmbed')) {
    /**
     * Generate URL embed YouTube dari video URL.
     * Dengan parameter rel=0 dan modestbranding=1.
     */
    function youtubeEmbed(string $url): ?string
    {
        $id = getYoutubeId($url);

        if (! $id) {
            return null;
        }

        return 'https://www.youtube.com/embed/' . $id . '?rel=0&modestbranding=1';
    }
}

if (! function_exists('youtubeThumb')) {
    /**
     * Ambil URL thumbnail YouTube dari video URL.
     *
     * @param string $size  Options: default|mqdefault|hqdefault|sddefault|maxresdefault
     */
    function youtubeThumb(string $url, string $size = 'hqdefault'): ?string
    {
        $id = getYoutubeId($url);

        if (! $id) {
            return null;
        }

        return "https://img.youtube.com/vi/{$id}/{$size}.jpg";
    }
}

if (! function_exists('formatDuration')) {
    /**
     * Format durasi dalam detik ke format jam:menit:detik atau menit:detik.
     *
     * @example formatDuration(3661) → "1:01:01"
     * @example formatDuration(125)  → "2:05"
     */
    function formatDuration(int $seconds): string
    {
        $hours   = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs    = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }
}

if (! function_exists('formatDurationHuman')) {
    /**
     * Format durasi menit ke format human-readable Indonesia.
     *
     * @example formatDurationHuman(90) → "1 jam 30 menit"
     * @example formatDurationHuman(45) → "45 menit"
     */
    function formatDurationHuman(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;

        if ($mins === 0) {
            return $hours . ' jam';
        }

        return $hours . ' jam ' . $mins . ' menit';
    }
}

if (! function_exists('avatarUrl')) {
    /**
     * Ambil URL avatar user. Bisa menerima User object atau (avatar, name).
     * Jika tidak ada avatar, gunakan UI Avatars sebagai fallback.
     */
    function avatarUrl($avatarOrUser, string $name = ''): string
    {
        // Jika argumen pertama adalah User object
        if ($avatarOrUser instanceof \App\Models\User) {
            $avatar = $avatarOrUser->avatar ?? null;
            $name   = $avatarOrUser->name ?? 'User';
        } else {
            $avatar = $avatarOrUser;
        }

        if ($avatar) {
            if (str_starts_with($avatar, 'http')) {
                return $avatar;
            }
            return \Illuminate\Support\Facades\Storage::url($avatar);
        }

        $encoded = urlencode($name ?: 'User');

        return "https://ui-avatars.com/api/?name={$encoded}&background=2563EB&color=fff&size=200";
    }
}

if (! function_exists('storageUrl')) {
    /**
     * Generate public URL dari path relatif storage.
     * Menggunakan asset() agar kompatibel dengan shared hosting.
     */
    function storageUrl(?string $path, string $default = ''): string
    {
        if (! $path) {
            return $default;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }
}

if (! function_exists('excerptText')) {
    /**
     * Potong teks HTML menjadi plain text dengan panjang tertentu.
     *
     * @example excerptText('<p>Lorem ipsum dolor sit amet</p>', 20) → "Lorem ipsum dolor si..."
     */
    function excerptText(string $html, int $limit = 160): string
    {
        $plain = strip_tags($html);
        $plain = preg_replace('/\s+/', ' ', trim($plain));

        if (mb_strlen($plain) <= $limit) {
            return $plain;
        }

        return mb_substr($plain, 0, $limit) . '...';
    }
}

if (! function_exists('send_notification')) {
    /**
     * Kirim notifikasi in-app ke user tertentu.
     *
     * @param \App\Models\User $user    Target user
     * @param string $type             Tipe: course|bootcamp|order|cert|success|warning|error|info
     * @param string $title            Judul notifikasi
     * @param string $message          Pesan notifikasi
     * @param string|null $url         URL tujuan (optional)
     */
    function send_notification(\App\Models\User $user, string $type, string $title, string $message, ?string $url = null): void
    {
        $user->notify(new \App\Notifications\AppNotification($type, $title, $message, $url));
    }
}
