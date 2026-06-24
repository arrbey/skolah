<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

/**
 * Backfill WebP variants untuk gambar JPG/PNG yang sudah di-upload sebelum
 * strategi WebP aktif (sebelum 2026-05-05).
 *
 * Cara pakai:
 *   php artisan images:backfill-webp --dry-run        (list file saja, no upload)
 *   php artisan images:backfill-webp                  (convert semua)
 *   php artisan images:backfill-webp --folder=courses (convert folder tertentu saja)
 *   php artisan images:backfill-webp --quality=80     (custom quality, default 85)
 *
 * Safe untuk dijalankan ulang: skip file yang sudah punya .webp sibling.
 */
class BackfillWebpImages extends Command
{
    protected $signature = 'images:backfill-webp
                            {--dry-run : List file saja, tidak upload}
                            {--folder= : Filter folder tertentu (misal "courses")}
                            {--quality=85 : WebP quality 1-100}
                            {--limit=0 : Batasi jumlah file (0 = no limit)}';

    protected $description = 'Generate WebP sibling untuk gambar JPG/PNG existing di MinIO';

    protected string $disk = 's3';

    public function handle(): int
    {
        $dryRun  = (bool) $this->option('dry-run');
        $folder  = $this->option('folder');
        $quality = (int) $this->option('quality');
        $limit   = (int) $this->option('limit');

        $this->info('═══════════════════════════════════════════════════');
        $this->info('  WebP Backfill Command');
        $this->info('═══════════════════════════════════════════════════');
        $this->line('Mode     : ' . ($dryRun ? '🔍 DRY RUN (no upload)' : '✍️  LIVE (will upload)'));
        $this->line('Folder   : ' . ($folder ?: '(all)'));
        $this->line('Quality  : ' . $quality);
        $this->line('Limit    : ' . ($limit ?: 'no limit'));
        $this->line('');

        $storage = Storage::disk($this->disk);

        $this->info('Listing files... ini bisa lama kalau banyak gambar.');
        $allFiles = $folder
            ? $storage->allFiles($folder)
            : $storage->allFiles();

        // Filter hanya jpg/jpeg/png
        $imageFiles = array_filter($allFiles, fn($f) => preg_match('/\.(jpe?g|png)$/i', $f));

        $this->info('Found ' . count($imageFiles) . ' JPG/PNG files.');

        $processed = 0;
        $skipped   = 0;
        $failed    = 0;
        $uploaded  = 0;

        $bar = $this->output->createProgressBar(count($imageFiles));
        $bar->start();

        $manager = new ImageManager(new GdDriver());

        foreach ($imageFiles as $path) {
            $bar->advance();
            $processed++;

            if ($limit > 0 && $uploaded >= $limit) {
                break;
            }

            $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);

            // Skip kalau WebP sudah ada
            // Note: beberapa MinIO config return 403 (bukan 404) untuk file non-existent,
            // jadi kita catch exception dan anggap file belum ada.
            try {
                if ($storage->exists($webpPath)) {
                    $skipped++;
                    continue;
                }
            } catch (\Throwable $e) {
                // Existence check gagal (403/network) — anggap belum ada, lanjut convert
            }

            if ($dryRun) {
                $uploaded++; // hitung sebagai "would upload"
                continue;
            }

            try {
                $content = $storage->get($path);
                if (! $content) {
                    $failed++;
                    continue;
                }

                $image       = $manager->read($content);
                $webpEncoded = $image->toWebp($quality);

                $storage->put($webpPath, (string) $webpEncoded, [
                    'visibility'   => 'public',
                    'ContentType'  => 'image/webp',
                    'CacheControl' => 'public, max-age=31536000',
                ]);

                $uploaded++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->warn("FAIL: {$path} — " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('═══════════════════════════════════════════════════');
        $this->line('Total processed  : ' . $processed);
        $this->line('Skipped (exists) : ' . $skipped);
        if ($dryRun) {
            $this->line('Would upload     : ' . $uploaded);
        } else {
            $this->line('Uploaded         : ' . $uploaded);
        }
        $this->line('Failed           : ' . $failed);
        $this->info('═══════════════════════════════════════════════════');

        if ($dryRun) {
            $this->comment('Ini DRY RUN. Jalankan tanpa --dry-run untuk upload beneran.');
        }

        return self::SUCCESS;
    }
}
