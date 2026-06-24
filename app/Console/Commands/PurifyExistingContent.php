<?php

namespace App\Console\Commands;

use App\Models\Benefit;
use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Bundle;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseReview;
use App\Models\FlashSale;
use App\Models\Gallery;
use App\Models\Institution;
use App\Models\LandingProgram;
use App\Models\MembershipPlan;
use App\Models\Post;
use App\Models\Quiz;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\HtmlSanitizerService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Security — One-time sanitize existing rich-text content in database.
 *
 * Setelah HtmlSanitizerService di-deploy, konten lama yang sudah
 * masuk ke database mungkin masih berisi payload XSS.
 * Jalankan command ini sekali untuk membersihkan semuanya.
 *
 * Usage:
 *   php artisan security:purify-existing-content --dry-run   # preview
 *   php artisan security:purify-existing-content --force       # execute
 */
class PurifyExistingContent extends Command
{
    protected $signature = 'security:purify-existing-content
                            {--dry-run : Tampilkan tanpa mengubah data}
                            {--force : Skip confirmation prompt}
                            {--chunk-size=500 : Jumlah record per batch}';

    protected $description = 'Re-sanitize all existing rich-text content via HtmlSanitizerService (one-time migration after XSS hardening)';

    /** @var array<class-string<Model>, list<non-empty-string>> */
    protected array $targets = [
        // ── Public Content ───────────────────────────────────────────────
        Course::class        => ['description', 'meta_description'],
        Book::class          => ['description', 'meta_description'],
        Bootcamp::class      => ['description', 'meta_description'],
        Bundle::class        => ['description'],
        Post::class          => ['content'],
        Gallery::class       => ['title', 'content'],

        // ── Lesson & Review ──────────────────────────────────────────────
        CourseLesson::class  => ['content'],
        CourseReview::class  => ['review'],

        // ── Instructor / Application ───────────────────────────────────
        Quiz::class          => ['description'],

        // ── Misc Content ───────────────────────────────────────────────
        MembershipPlan::class=> ['description'],
        FlashSale::class     => ['description'],
        Campus::class        => ['description'],
        Institution::class => ['description'],
        LandingProgram::class=> ['title', 'subtitle', 'description'],
        Benefit::class       => ['title', 'subtitle'],
        Testimonial::class   => ['content'],

        // ── User Profile ───────────────────────────────────────────────
        User::class          => ['bio'],
    ];

    public function handle(HtmlSanitizerService $sanitizer): int
    {
        $isDryRun = $this->option('dry-run');
        $chunk    = (int) $this->option('chunk-size');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN — Tidak ada data yang diubah.');
        }

        if (! $isDryRun && ! $this->option('force')) {
            if (! $this->confirm('⚠️  Ini akan memodifikasi konten rich-text di database. Backup dahulu. Lanjutkan?')) {
                $this->info('Dibatalkan.');
                return self::SUCCESS;
            }
        }

        $totalChanged  = 0;
        $totalScanned  = 0;
        $totalModels   = count($this->targets);
        $currentModel  = 0;

        foreach ($this->targets as $modelClass => $fields) {
            $currentModel++;
            $label = class_basename($modelClass);

            $this->newLine();
            $this->info("[{$currentModel}/{$totalModels}] {$label}");

            $bar = $this->output->createProgressBar();

            $modelClass::query()
                ->select(array_merge(['id'], $fields))
                ->chunkById($chunk, function ($records) use ($sanitizer, $fields, $label, &$totalChanged, &$totalScanned, $bar) {
                    foreach ($records as $record) {
                        $totalScanned++;
                        $dirty = false;

                        foreach ($fields as $field) {
                            $original = $record->{$field};
                            if ($original === null || trim($original) === '') {
                                continue;
                            }

                            $cleaned = $sanitizer->clean($original);

                            if ($cleaned !== $original) {
                                $record->{$field} = $cleaned;
                                $dirty = true;
                            }
                        }

                        if ($dirty) {
                            $totalChanged++;
                            if (! $this->option('dry-run')) {
                                $record->save();
                            }
                        }

                        $bar->advance();
                    }
                });

            $bar->finish();
            $this->newLine();
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Model', $totalModels],
                ['Records Scanned', number_format($totalScanned)],
                ['Records Changed', number_format($totalChanged)],
                ['Mode', $isDryRun ? 'DRY RUN (no DB changes)' : 'LIVE'],
            ]
        );

        if (! $isDryRun && $totalChanged > 0) {
            Log::info('security:purify-existing-content executed', [
                'records_scanned' => $totalScanned,
                'records_changed' => $totalChanged,
                'models' => array_keys($this->targets),
            ]);

            $this->info('✅ Semua konten lama telah di-sanitize.');
            $this->warn('💡 Jangan lupa: clear cache view/config/route jika ada perubahan blade.');
        } else {
            $this->info('Tidak ada perubahan yang diperlukan.');
        }

        return self::SUCCESS;
    }
}
