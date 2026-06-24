<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    /**
     * Generate and return sitemap.xml.
     * Cached 1 hour to avoid hammering DB on every crawler hit.
     *
     * Route: GET /sitemap.xml
     */
    public function index()
    {
        $xml = Cache::remember('sitemap.xml_v1', 3600, function () {
            return $this->build()->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    protected function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        // ── Static pages ─────────────────────────────────────────────────────
        $staticPages = [
            ['url' => url('/'),              'priority' => '1.0',  'changefreq' => 'daily'],
            ['url' => route('courses.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('bootcamps.index'),'priority' => '0.9','changefreq' => 'daily'],
            ['url' => route('books.index'),   'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('membership'),    'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => url('/about'),          'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => url('/contact'),        'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => url('/blog'),           'priority' => '0.6', 'changefreq' => 'weekly'],
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setPriority((float) $page['priority'])
                    ->setChangeFrequency($page['changefreq'])
            );
        }

        // ── Published courses ─────────────────────────────────────────────────
        Course::published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->chunk(200, function ($courses) use ($sitemap) {
                foreach ($courses as $course) {
                    $sitemap->add(
                        Url::create(route('courses.show', $course->slug))
                            ->setLastModificationDate(Carbon::parse($course->updated_at))
                            ->setPriority(0.8)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });

        // ── Upcoming / ongoing bootcamps ──────────────────────────────────────
        Bootcamp::whereIn('status', ['upcoming', 'ongoing'])
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->chunk(200, function ($bootcamps) use ($sitemap) {
                foreach ($bootcamps as $bootcamp) {
                    $sitemap->add(
                        Url::create(route('bootcamps.show', $bootcamp->slug))
                            ->setLastModificationDate(Carbon::parse($bootcamp->updated_at))
                            ->setPriority(0.7)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    );
                }
            });

        // ── Published books ───────────────────────────────────────────────────
        Book::published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->chunk(200, function ($books) use ($sitemap) {
                foreach ($books as $book) {
                    $sitemap->add(
                        Url::create(route('books.show', $book->slug))
                            ->setLastModificationDate(Carbon::parse($book->updated_at))
                            ->setPriority(0.7)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });

        return $sitemap;
    }
}
